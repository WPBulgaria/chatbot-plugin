<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\ChatModel;
use WPBulgaria\Chatbot\Validators\Chat\ChatValidator;

defined( 'ABSPATH' ) || exit;

class ChatAction {

    static function list(\WP_REST_Request $request) {
        $params = $request->get_params();
        $perPage = isset($params['per_page']) ? absint($params['per_page']) : 20;
        $page = isset($params['page']) ? absint($params['page']) : 1;
        $userId = isset($params['user_id']) ? absint($params['user_id']) : 0;

        $result = ChatModel::list($userId, $perPage, $page);

        return new \WP_REST_Response([
            "success" => true,
            "chats"   => $result['chats'],
            "total"   => $result['total'],
            "pages"   => $result['pages']
        ], 200);
    }

    static function get(\WP_REST_Request $request) {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        $chat = ChatModel::get($id);

        if (!$chat) {
            return new \WP_REST_Response(["success" => false, "message" => "Chat not found"], 404);
        }

        return new \WP_REST_Response([
            "success" => true,
            "chat"    => $chat
        ], 200);
    }

    static function chat(\WP_REST_Request $request) {
        if (\WPBulgaria\Chatbot\Functions\user_rate_limit_exceeded()) {
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
            $result = ChatModel::chat($message, $chatId);

            return new \WP_REST_Response([
                "success"  => true,
                "chat"     => $result,
            ], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    static function updateTitle(\WP_REST_Request $request) {
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
            ChatModel::updateTitle($id, $title);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    static function trash(\WP_REST_Request $request) {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            ChatModel::trash($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    static function remove(\WP_REST_Request $request) {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            ChatModel::remove($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    static function restore(\WP_REST_Request $request) {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chat ID"], 400);
        }

        try {
            ChatModel::restore($id);

            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }
}
