<?php

namespace WPBulgaria\Chatbot\Models;

use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Data\FileSearch;
use Gemini\Data\Tool;
use Gemini\Data\GenerationConfig;
use WPBulgaria\Chatbot\Models\SearchFileModel;

defined( 'ABSPATH' ) || exit;

class ChatModel {

    const POST_TYPE = 'wpb_chat';
    const META_MESSAGES = '_wpb_chat_messages';

    public static function getClient() {
        $configs = ConfigsModel::view();
        if (empty($configs["apiKey"])) {
            return false;
        }

        return Gemini::client($configs["apiKey"]);
    }

    public static function list(int $userId = 0, int $perPage = 20, int $page = 1): array {
        $args = [
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($userId > 0 && current_user_can('edit_others_posts')) {
            $args['author'] = $userId;
        } else if(!current_user_can('edit_others_posts')) {
            $args['author'] = get_current_user_id();
        }

        $query = new \WP_Query($args);
        $chats = [];

        foreach ($query->posts as $post) {
            $chats[] = self::formatChat($post);
        }

        return [
            'chats' => $chats,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages
        ];
    }

    public static function get(int $id): ?array {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE || $post->post_status === 'trash') {
            return null;
        }

        return self::formatChat($post, true);
    }

    protected static function getFileSearchStore() {
        $configs = ConfigsModel::view();
        if (empty($configs["fileSearchStore"])) {
            return null;
        }

        return SearchFileModel::getFileSearchStore($configs["fileSearchStore"]);
    }


    public static function stream(string $message, ?int $chatId = null, ?int $userId = null): array {
        $client = self::getClient();
        if (!$client) {
            throw new \Exception("API key not configured", 400);
        }

        $userId = $userId ?? get_current_user_id();
        $isNewChat = empty($chatId);
        $chat = $isNewChat ? null : self::get($chatId);

        if (!$isNewChat && !$chat) {
            throw new \Exception("Chat not found", 404);
        }

        $messages = $isNewChat ? [] : $chat['messages'];

        if ($isNewChat) {
            $title = self::generateTitle($message);
            $chatId = self::create($title, $messages, $userId);
        } 

        $messages[] = [
            'role'      => 'user',
            'content'   => $message,
            'createdAt' => date(DATE_ATOM),
        ];

        $history = self::buildGeminiHistory($messages);
        $responseText = '';

        try {
            $configs = ConfigsModel::view();
            $model = $configs["model"] ?? "gemini-2.5-flash";
            
            $model = $client->generativeModel($model);

            $generateConfig = new GenerationConfig(
                temperature: 0.1,       // Keep low (0.0 - 0.2) for strict adherence to facts in files.
                maxOutputTokens: 800,   // 4096 is overkill and risky. 800 covers ~350 words, plenty for your limit.
                topP: 0.8,              // Lowering slightly reduces "creative" padding words.
                topK: 20,               // Forces the model to pick the most likely words faster (more direct).
                stopSequences: [],
            );

            $model->withGenerationConfig($generateConfig);

            if (!empty($configs["systemInstructions"])) {
                $model->withSystemInstruction(Content::parse(part: $configs["systemInstructions"]));
            }

           

            try {
                $fileSearchStore = self::getFileSearchStore();

                if ($fileSearchStore) {
                $model->withTool(new Tool(
                        fileSearch: new FileSearch(fileSearchStoreNames: [$fileSearchStore]),
                    ));
                }
            } catch (\Exception $e) {
                error_log("Failed to add file search store to model: " . $e->getMessage());
            }
     

            $stream = $model->startChat(history: $history)->streamSendMessage($message);

            // Close the session to prevent blocking
            session_write_close();
            $responseText = '';
            foreach ($stream as $chunk) {
                // Extract text from this specific chunk
                $chunkText = '';

                // Check if there are candidates and parts before trying to read them
                if ($chunk->candidates && count($chunk->candidates) > 0) {
                    foreach ($chunk->parts() as $part) {
                        // Only append if it is actually a TextPart
                        if (!empty($part->text)) {
                            $chunkText .= $part->text;
                            $responseText .= $part->text;
                        }
                    }
                }
                
                // If the chunk is empty, skip it to avoid confusing the client
                if (empty($chunkText)) {
                    continue;
                }

                // We format this as a Server-Sent Event (SSE) data line.
                // Sending JSON ensures safely escaping newlines/quotes.
                $payload = json_encode(['success' => true, 'message' => $chunkText, 'chatId' => $chatId, 'isNew' => $isNewChat, 'title' => $title], JSON_UNESCAPED_UNICODE);
                
                echo "data: " . $payload . "\n\n";

                // FLUSH BUFFER IMMEDIATELY
                // This forces PHP to send the data to the browser right now
                if (ob_get_level() > 0) {
                    ob_end_flush();
                }
                flush();
            }

            

            $messages[] = [
                'role'      => 'model',
                'content'   => $responseText,
                'createdAt' => date(DATE_ATOM),
            ];

            
            self::updateMessages($chatId, $messages);
            return [];
        } catch (\Exception $e) {
            throw new \Exception("Failed to get AI response: " . $e->getMessage(), 500);
        }
    }


    public static function chat(string $message, ?int $chatId = null, ?int $userId = null): array {
        $client = self::getClient();
        if (!$client) {
            throw new \Exception("API key not configured", 400);
        }

        $userId = $userId ?? get_current_user_id();
        $isNewChat = empty($chatId);
        $chat = $isNewChat ? null : self::get($chatId);

        if (!$isNewChat && !$chat) {
            throw new \Exception("Chat not found", 404);
        }

        $messages = $isNewChat ? [] : $chat['messages'];

        $messages[] = [
            'role'      => 'user',
            'content'   => $message,
            'createdAt' => date(DATE_ATOM),
        ];

        $history = self::buildGeminiHistory($messages);

        try {
            $configs = ConfigsModel::view();
            $model = $configs["model"] ?? "gemini-2.5-flash";
            
            $model = $client->generativeModel($model);

            if (!empty($configs["systemInstructions"])) {
                $model->withSystemInstruction(Content::parse(part: $configs["systemInstructions"]));
            }

           

            try {
                $fileSearchStore = self::getFileSearchStore();

                if ($fileSearchStore) {
                $model->withTool(new Tool(
                        fileSearch: new FileSearch(fileSearchStoreNames: [$fileSearchStore]),
                    ));
                }
            } catch (\Exception $e) {
                error_log("Failed to add file search store to model: " . $e->getMessage());
            }
     

            $response = $model->startChat(history: $history)->sendMessage($message);
            $responseText = $response->text();

            $messages[] = [
                'role'      => 'model',
                'content'   => $responseText,
                'createdAt' => date(DATE_ATOM),
            ];

            if ($isNewChat) {
                $title = self::generateTitle($message);
                $chatId = self::create($title, $messages, $userId);
            } else {
                self::updateMessages($chatId, $messages);
            }

            return [
                'chatId'   => $chatId,
                'message'  => $responseText,
                'isNew'    => $isNewChat,
                'title'    => $isNewChat ? $title : $chat['title'],
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to get AI response: " . $e->getMessage(), 500);
        }
    }

    public static function create(string $title, array $messages = [], int $userId = 0): int {
        $userId = $userId ?: get_current_user_id();

        $postId = wp_insert_post([
            'post_type'   => self::POST_TYPE,
            'post_title'  => sanitize_text_field($title),
            'post_status' => 'publish',
            'post_author' => $userId,
        ], true);

        if (is_wp_error($postId)) {
            throw new \Exception("Failed to create chat: " . $postId->get_error_message(), 500);
        }

        update_post_meta($postId, self::META_MESSAGES, $messages);

        return $postId;
    }

    public static function updateMessages(int $id, array $messages): bool {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        update_post_meta($id, self::META_MESSAGES, $messages);
        
        wp_update_post([
            'ID'            => $id,
            'post_modified' => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', true),
        ]);

        return true;
    }

    public static function updateTitle(int $id, string $title): bool {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = wp_update_post([
            'ID'         => $id,
            'post_title' => sanitize_text_field($title),
        ], true);

        if (is_wp_error($result)) {
            throw new \Exception("Failed to update chat title", 500);
        }

        return true;
    }

    public static function trash(int $id): bool {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = wp_trash_post($id);

        if (!$result) {
            throw new \Exception("Failed to trash chat", 500);
        }

        return true;
    }

    public static function remove(int $id): bool {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = wp_delete_post($id, true);

        if (!$result) {
            throw new \Exception("Failed to delete chat", 500);
        }

        return true;
    }

    public static function restore(int $id): bool {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = wp_untrash_post($id);

        if (!$result) {
            throw new \Exception("Failed to restore chat", 500);
        }

        return true;
    }

    private static function formatChat(\WP_Post $post, bool $includeMessages = false): array {
        $messages = [];
        
        if ($includeMessages) {
            $messagesJson = get_post_meta($post->ID, self::META_MESSAGES, true);
            $messages = $messagesJson ? json_decode($messagesJson, true, JSON_UNESCAPED_UNICODE) : [];
            $messages = is_array($messages) ? $messages : [];
        }

        $messageCount = 0;
        if (!$includeMessages) {
            $messagesJson = get_post_meta($post->ID, self::META_MESSAGES, true);
            $messages = $messagesJson ? json_decode($messagesJson, true, JSON_UNESCAPED_UNICODE) : [];
            $messages = is_array($messages) ? $messages : [];
            $messageCount = count($messages);
        }

        $result = [
            'id'          => $post->ID,
            'title'       => $post->post_title ?: "Untitled Chat",
            'userId'      => (int) $post->post_author,
            'userName'    => get_the_author_meta('display_name', $post->post_author),
            'createdAt'   => $post->post_date_gmt,
            'modifiedAt'  => $post->post_modified_gmt,
        ];

        if ($includeMessages) {
            $result['messages'] = $messages;
            $result['messageCount'] = count($messages);
        } else {
            $result['messageCount'] = $messageCount;
        }

        return $result;
    }

    private static function generateTitle(string $message): string {
        $title = substr($message, 0, 50);
        if (strlen($message) > 50) {
            $title .= "...";
        }
        return sanitize_text_field($title);
    }

    private static function buildGeminiHistory(array $messages): array {
        $history = [];
        
        $messagesToProcess = array_slice($messages, 0, -1);
        
        foreach ($messagesToProcess as $msg) {
            $history[] = Content::parse(part: $msg["content"], role: $msg["role"] === 'model' ? Role::MODEL : Role::USER);
        }

        return $history;
    }
}
