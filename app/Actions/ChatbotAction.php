<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\ChatbotModel;
use WPBulgaria\Chatbot\Validators\Chatbot\ChatbotValidator;

defined('ABSPATH') || exit;

/**
 * Chatbot Action - handles chatbot-related REST API endpoints
 */
class ChatbotAction {

    /**
     * List all chatbots
     */
    public static function list(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $perPage = isset($params['per_page']) ? absint($params['per_page']) : 20;
        $page = isset($params['page']) ? absint($params['page']) : 1;

        try {
            $result = wpb_chatbot_app(ChatbotModel::class)->list($perPage, $page);

            return new \WP_REST_Response([
                "success"  => true,
                "chatbots" => $result['chatbots'],
                "total"    => $result['total'],
                "pages"    => $result['pages']
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
     * Get a single chatbot
     */
    public static function get(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        try {
            $chatbot = wpb_chatbot_app(ChatbotModel::class)->get($id);

            if (!$chatbot) {
                return new \WP_REST_Response([
                    "success" => false,
                    "message" => "Chatbot not found"
                ], 404);
            }

            return new \WP_REST_Response([
                "success" => true,
                "chatbot" => $chatbot
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
     * Create a new chatbot
     */
    public static function create(\WP_REST_Request $request): \WP_REST_Response {
        $body = $request->get_json_params();

        $validator = ChatbotValidator::make();

        if (!$validator->isValid($body)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        try {
            $chatbotId = wpb_chatbot_app(ChatbotModel::class)->create($validator->getCleanData($body));

            return new \WP_REST_Response([
                "success"   => true,
                "chatbotId" => $chatbotId
            ], 201);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Update a chatbot
     */
    public static function update(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $body = $request->get_json_params();

        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        $validator = ChatbotValidator::make();

        if (!$validator->isValid($body)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatbotModel::class)->update($id, $validator->getCleanData($body));

            return new \WP_REST_Response([
                "success" => true
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
     * Update chatbot config
     */
    public static function updateConfig(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $body = $request->get_json_params();

        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        $validator = ChatbotValidator::make();

        if (!$validator->validateConfig($body)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => $validator->getErrors()
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatbotModel::class)->updateConfig($id, $validator->getCleanData($body));

            return new \WP_REST_Response([
                "success" => true
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
     * Trash a chatbot (soft delete)
     */
    public static function trash(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatbotModel::class)->trash($id);

            return new \WP_REST_Response([
                "success" => true
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
     * Remove a chatbot (hard delete)
     */
    public static function remove(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatbotModel::class)->remove($id);

            return new \WP_REST_Response([
                "success" => true
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
     * Restore a trashed chatbot
     */
    public static function restore(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $id = isset($params['id']) ? absint($params['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid chatbot ID"
            ], 400);
        }

        try {
            wpb_chatbot_app(ChatbotModel::class)->restore($id);

            return new \WP_REST_Response([
                "success" => true
            ], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }
}
