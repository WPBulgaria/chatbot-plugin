<?php

namespace WPBulgaria\Chatbot\Models;

use WPBulgaria\Chatbot\Services\GeminiService;
use WPBulgaria\Chatbot\Contracts\AuthInterface;

defined('ABSPATH') || exit;

class ChatModel extends BaseModel {

    const POST_TYPE = 'wpb_chat';
    const META_MESSAGES = '_wpb_chat_messages';
    protected GeminiService $geminiService;
    protected PostModel $postModel;
    protected AuthInterface $auth;

    public function __construct(GeminiService $geminiService, PostModel $postModel, AuthInterface $auth) {
        parent::__construct();
        $this->geminiService = $geminiService;
        $this->postModel = $postModel;
        $this->auth = $auth;
    }

    /**
     * Prepare chat context for sending messages
     * Reduces duplication between chat() and stream() methods
     * 
     * @return array{geminiService: GeminiService, userId: int, isNewChat: bool, chat: array|null, messages: array}
     */
    protected function prepareChat(?int $chatId, ?int $userId = null): array {
        if (!$this->geminiService->isConfigured()) {
            throw new \Exception("API key not configured", 400);
        }

        $userId = $userId ?? $this->auth->currentUserId();
        $isNewChat = empty($chatId);
        $chat = $isNewChat ? null : $this->get($chatId);

        if (!$isNewChat && !$chat) {
            throw new \Exception("Chat not found", 404);
        }

        $messages = $isNewChat ? [] : ($chat['messages'] ?? []);

        return [
            'geminiService' => $this->geminiService,
            'userId'        => $userId,
            'isNewChat'     => $isNewChat,
            'chat'          => $chat,
            'messages'      => $messages,
        ];
    }

    /**
     * Add user message to messages array
     */
    protected function addUserMessage(array &$messages, string $message): void {
        $messages[] = $this->geminiService->createMessage('user', $message);
    }

    /**
     * Add model response to messages array
     */
    protected function addModelMessage(array &$messages, string $response): void {
        $messages[] = $this->geminiService->createMessage('model', $response);
    }

    /**
     * List chats with pagination
     */
    public function list(int $userId = 0, int $perPage = 20, int $page = 1): array {
        $args = [
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($userId > 0 && $this->auth->currentUserCan('edit_others_posts')) {
            $args['author'] = $userId;
        } else if (!$this->auth->currentUserCan('edit_others_posts')) {
            $args['author'] = $this->auth->currentUserId();
        }

        $query = new \WP_Query($args);
        $chats = [];

        foreach ($query->posts as $post) {
            $chats[] = $this->formatChat($post);
        }

        return [
            'chats' => $chats,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages
        ];
    }

    /**
     * Get a single chat by ID
     */
    public function get(int $id): ?array {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE || $post->post_status === 'trash') {
            return null;
        }

        return $this->formatChat($post, true);
    }

    /**
     * Stream chat response via SSE
     */
    public function stream(string $message, ?int $chatId = null, ?int $userId = null): array {
        $context = $this->prepareChat($chatId, $userId);

        /** @var GeminiService $geminiService */
        $geminiService = $context['geminiService'];
        /** @var int $contextUserId */
        $contextUserId = $context['userId'];
        /** @var bool $isNewChat */
        $isNewChat = $context['isNewChat'];
        /** @var array|null $chat */
        $chat = $context['chat'];
        /** @var array $messages */
        $messages = $context['messages'];

        // Create chat early for streaming so we have an ID
        $title = '';
        $streamChatId = $chatId;
        if ($isNewChat) {
            $title = $this->generateTitle($message);
            $streamChatId = $this->create($title, $messages, $contextUserId);
        }

        $this->addUserMessage($messages, $message);
        $history = $geminiService->buildHistory($messages);

        try {
            $responseText = $geminiService->streamMessage(
                $message,
                $history,
                function ($chunkText) use ($streamChatId, $isNewChat, $title) {
                    $payload = json_encode([
                        'success' => true,
                        'message' => $chunkText,
                        'chatId'  => $streamChatId,
                        'isNew'   => $isNewChat,
                        'title'   => $title
                    ], JSON_UNESCAPED_UNICODE);

                    echo "data: " . $payload . "\n\n";

                    if (ob_get_level() > 0) {
                        ob_end_flush();
                    }
                    flush();
                }
            );

            $this->addModelMessage($messages, $responseText);
            $this->updateMessages($streamChatId, $messages);

            return [];
        } catch (\Exception $e) {
            throw new \Exception("Failed to get AI response: " . $e->getMessage(), 500);
        }
    }

    /**
     * Send chat message and get response
     */
    public function chat(string $message, ?int $chatId = null, ?int $userId = null): array {
        $context = $this->prepareChat($chatId, $userId);

        /** @var GeminiService $geminiService */
        $geminiService = $context['geminiService'];
        /** @var int $contextUserId */
        $contextUserId = $context['userId'];
        /** @var bool $isNewChat */
        $isNewChat = $context['isNewChat'];
        /** @var array|null $chat */
        $chat = $context['chat'];
        /** @var array $messages */
        $messages = $context['messages'];

        $this->addUserMessage($messages, $message);
        $history = $geminiService->buildHistory($messages);

        try {
            $responseText = $geminiService->sendMessage($message, $history);

            $this->addModelMessage($messages, $responseText);

            $title = '';
            $resultChatId = $chatId;
            if ($isNewChat) {
                $title = $this->generateTitle($message);
                $resultChatId = $this->create($title, $messages, $contextUserId);
            } else {
                $this->updateMessages($chatId, $messages);
                $title = $chat['title'] ?? '';
            }

            return [
                'chatId'  => $resultChatId,
                'message' => $responseText,
                'isNew'   => $isNewChat,
                'title'   => $title,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to get AI response: " . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new chat
     */
    public function create(string $title, array $messages = [], int $userId = 0): int {
        $userId = $userId ?: $this->auth->currentUserId();

        $postId = $this->postModel->insert([
            'post_type'   => self::POST_TYPE,
            'post_title'  => sanitize_text_field($title),
            'post_status' => 'publish',
            'post_author' => $userId,
        ], true);

        if (is_wp_error($postId)) {
            throw new \Exception("Failed to create chat: " . $postId->get_error_message(), 500);
        }

        $this->postModel->updateMeta($postId, self::META_MESSAGES, $messages);

        return $postId;
    }

    /**
     * Get chat messages
     */
    public function getMessages(int|string $id): array {
        $message = $this->postModel->getMeta($id, self::META_MESSAGES);
        if (!is_array($message) && is_string($message)) {
            $message = json_decode($message, true, JSON_UNESCAPED_UNICODE);
            $message = is_array($message) ? $message : [];
        }
        return $message;
    }

    /**
     * Update chat messages
     */
    public function updateMessages(int $id, array $messages): bool {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $this->postModel->updateMeta($id, self::META_MESSAGES, $messages);

        $this->postModel->update([
            'ID'                => $id,
            'post_modified'     => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', true),
        ]);

        return true;
    }

    /**
     * Update chat title
     */
    public function updateTitle(int $id, string $title): bool {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = $this->postModel->update([
            'ID'         => $id,
            'post_title' => sanitize_text_field($title),
        ], true);

        if (is_wp_error($result)) {
            throw new \Exception("Failed to update chat title", 500);
        }

        return true;
    }

    /**
     * Trash a chat (soft delete)
     */
    public function trash(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = $this->postModel->trash($id);

        if (!$result) {
            throw new \Exception("Failed to trash chat", 500);
        }

        return true;
    }

    /**
     * Permanently delete a chat
     */
    public function remove(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = $this->postModel->delete($id);

        if (!$result) {
            throw new \Exception("Failed to delete chat", 500);
        }

        return true;
    }

    /**
     * Restore a trashed chat
     */
    public function restore(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chat not found", 404);
        }

        $result = $this->postModel->untrash($id);

        if (!$result) {
            throw new \Exception("Failed to restore chat", 500);
        }

        return true;
    }

    /**
     * Format chat post for API response
     */
    private function formatChat(\WP_Post $post, bool $includeMessages = false): array {
        $messages = $this->postModel->getMeta($post->ID, self::META_MESSAGES);

        if (!is_array($messages) && is_string($messages)) {
            $messages = json_decode($messages, true, JSON_UNESCAPED_UNICODE);
            $messages = is_array($messages) ? $messages : [];
        }

        $result = [
            'id'           => $post->ID,
            'title'        => $post->post_title ?: "Untitled Chat",
            'userId'       => (int) $post->post_author,
            'userName'     => get_the_author_meta('display_name', $post->post_author),
            'createdAt'    => $post->post_date_gmt,
            'modifiedAt'   => $post->post_modified_gmt,
            'messageCount' => count($messages),
        ];

        if ($includeMessages) {
            $result['messages'] = $messages;
        }

        return $result;
    }

    /**
     * Generate a title from the first message
     */
    private static function generateTitle(string $message): string {
        $title = substr($message, 0, 50);
        if (strlen($message) > 50) {
            $title .= "...";
        }
        return sanitize_text_field($title);
    }
}
