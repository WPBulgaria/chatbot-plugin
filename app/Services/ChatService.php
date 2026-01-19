<?php

namespace WPBulgaria\Chatbot\Services;

use WPBulgaria\Chatbot\Models\ChatModel;

defined('ABSPATH') || exit;

/**
 * Chat Service - handles chat business logic with dependency injection
 */
class ChatService {

    private GeminiService $geminiService;

    public function __construct(GeminiService $geminiService) {
        $this->geminiService = $geminiService;
    }

    /**
     * Get the Gemini service
     */
    public function getGeminiService(): GeminiService {
        return $this->geminiService;
    }

    /**
     * Prepare chat context for sending messages
     * 
     * @return array{userId: int, isNewChat: bool, chat: array|null, messages: array}
     */
    public function prepareChat(?int $chatId, ?int $userId = null): array {
        if (!$this->geminiService->isConfigured()) {
            throw new \Exception("API key not configured", 400);
        }

        $userId = $userId ?? get_current_user_id();
        $isNewChat = empty($chatId);
        $chat = $isNewChat ? null : ChatModel::get($chatId);

        if (!$isNewChat && !$chat) {
            throw new \Exception("Chat not found", 404);
        }

        $messages = $isNewChat ? [] : ($chat['messages'] ?? []);

        return [
            'userId'   => $userId,
            'isNewChat' => $isNewChat,
            'chat'     => $chat,
            'messages' => $messages,
        ];
    }

    /**
     * Send a chat message and get response
     */
    public function sendMessage(string $message, ?int $chatId = null, ?int $userId = null): array {
        $context = $this->prepareChat($chatId, $userId);

        $messages = $context['messages'];
        $isNewChat = $context['isNewChat'];
        $chat = $context['chat'];
        $contextUserId = $context['userId'];

        $this->addUserMessage($messages, $message);
        $history = $this->geminiService->buildHistory($messages);

        try {
            $responseText = $this->geminiService->sendMessage($message, $history);

            $this->addModelMessage($messages, $responseText);

            $title = '';
            $resultChatId = $chatId;

            if ($isNewChat) {
                $title = $this->generateTitle($message);
                $resultChatId = ChatModel::create($title, $messages, $contextUserId);
            } else {
                ChatModel::updateMessages($chatId, $messages);
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
     * Stream a chat message response via SSE
     */
    public function streamMessage(string $message, ?int $chatId = null, ?int $userId = null): void {
        $context = $this->prepareChat($chatId, $userId);

        $messages = $context['messages'];
        $isNewChat = $context['isNewChat'];
        $contextUserId = $context['userId'];

        // Create chat early for streaming so we have an ID
        $title = '';
        $streamChatId = $chatId;

        if ($isNewChat) {
            $title = $this->generateTitle($message);
            $streamChatId = ChatModel::create($title, $messages, $contextUserId);
        }

        $this->addUserMessage($messages, $message);
        $history = $this->geminiService->buildHistory($messages);

        try {
            $responseText = $this->geminiService->streamMessage(
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
            ChatModel::updateMessages($streamChatId, $messages);
        } catch (\Exception $e) {
            throw new \Exception("Failed to get AI response: " . $e->getMessage(), 500);
        }
    }

    /**
     * Add user message to messages array
     */
    protected function addUserMessage(array &$messages, string $message): void {
        $messages[] = GeminiService::createMessage('user', $message);
    }

    /**
     * Add model response to messages array
     */
    protected function addModelMessage(array &$messages, string $response): void {
        $messages[] = GeminiService::createMessage('model', $response);
    }

    /**
     * Generate a title from the first message
     */
    protected function generateTitle(string $message): string {
        $title = substr($message, 0, 50);
        if (strlen($message) > 50) {
            $title .= "...";
        }
        return sanitize_text_field($title);
    }
}
