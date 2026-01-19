<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\ChatModel;
use WPBulgaria\Chatbot\Services\ChatService;
use WPBulgaria\Chatbot\Validators\Chat\ChatValidator;
use function WPBulgaria\Chatbot\Functions\user_rate_limit_exceeded;

defined('ABSPATH') || exit;

/**
 * Chat Action - handles chat-related REST API endpoints
 * Demonstrates both static methods and DI patterns
 */
class ChatAction {

    /**
     * Get ChatService from container
     */
    protected static function getChatService(): ChatService {
        return wpb_chatbot_app(ChatService::class);
    }

    /**
     * List all chats
     */
    public static function list(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $perPage = isset($params['per_page']) ? absint($params['per_page']) : 20;
        $page = isset($params['page']) ? absint($params['page']) : 1;
        $userId = isset($params['user_id']) ? absint($params['user_id']) : 0;

        $result = wpb_chatbot_app(ChatModel::class)->list($userId, $perPage, $page);

        return new \WP_REST_Response([
            "success" => true,
            "chats"   => $result['chats'],
            "total"   => $result['total'],
            "pages"   => $result['pages']
        ], 200);
    }

    /**
     * Get a single chat
     */
    public static function get(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        $chat = wpb_chatbot_app(ChatModel::class)->get($id);

        if (!$chat) {
            return new \WP_REST_Response(["success" => false, "message" => "Chat not found"], 404);
        }

        return new \WP_REST_Response([
            "success" => true,
            "chat"    => $chat
        ], 200);
    }

    /**
     * Send a chat message (uses DI via ChatService)
     */
    public static function chat(\WP_REST_Request $request): \WP_REST_Response {
        if (user_rate_limit_exceeded()) {
            return new \WP_REST_Response(["success" => false, "message" => "Rate limit exceeded"], 429);
        }

        $params = $request->get_params();
        $body = $request->get_json_params();

        $message = $body['message'] ?? '';
        $chatId = isset($params['id']) ? absint($params['id']) : null;

        $validator = ChatValidator::make();

        if (!$validator->validateMessage($message)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        if ($chatId && !$validator->validateChatId($chatId)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        try {
            // Use ChatService via DI
            $chatService = self::getChatService();
            $result = wpb_chatbot_app(ChatModel::class)->chat($message, $chatId);

            return new \WP_REST_Response([
                "success" => true,
                "chat"    => $result,
            ], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Update chat title
     */
    public static function updateTitle(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $body = $request->get_json_params();

        $id = isset($params['id']) ? absint($params['id']) : 0;
        $title = $body['title'] ?? '';

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        $validator = ChatValidator::make();

        if (!$validator->validateTitle($title)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatModel::class)->updateTitle($id, $title);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Trash a chat (soft delete)
     */
    public static function trash(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            wpb_chatbot_app(ChatModel::class)->trash($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Remove a chat (hard delete)
     */
    public static function remove(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            wpb_chatbot_app(ChatModel::class)->remove($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Restore a trashed chat
     */
    public static function restore(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            wpb_chatbot_app(ChatModel::class)->restore($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Stream chat response via SSE (uses DI via ChatService)
     */
    public static function stream(\WP_REST_Request $request): void {
        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('X-SSE: 1');

        if (user_rate_limit_exceeded()) {
            echo "event: error\n";
            echo "data: " . json_encode(['success' => false, 'message' => "Rate limit exceeded", "code" => 429]) . "\n\n";
            flush();
            exit();
        }

        $params = $request->get_params();
        $body = $request->get_json_params();

        $message = $body['message'] ?? '';
        $chatId = isset($params['id']) ? absint($params['id']) : null;

        $validator = ChatValidator::make();

        if (!$validator->validateMessage($message)) {
            // Handle API errors (e.g., send a specialized error event)
            echo "event: error\n";
            echo "data: " . json_encode(['success' => false, 'message' => $validator->getErrors(), "code" => 400]) . "\n\n";
            flush();
            exit();
        }

        if ($chatId && !$validator->validateChatId($chatId)) {
            // Handle API errors (e.g., send a specialized error event)
            echo "event: error\n";
            echo "data: " . json_encode(['success' => false, 'message' => $validator->getErrors(), "code" => 400]) . "\n\n";
            flush();
            exit();
        }

        try {
            // Use ChatService via DI
            wpb_chatbot_app(ChatModel::class)->stream($message, $chatId);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            echo "event: error\n";
            echo "data: " . json_encode(['success' => false, 'message' => $e->getMessage(), "code" => $code]) . "\n\n";
            flush();
        }

        // Kill WP execution to prevent standard footer/JSON returns
        exit();
    }
}
