<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Validators\Configs\ConfigsValidator;

defined( 'ABSPATH' ) || exit;

class ConfigsAction {

    static function view(\WP_REST_Request $request) {
        try {
            $chatbotId = $request->get_param('chatbot_id');

            if (empty($chatbotId)) {
                return new \WP_REST_Response(["success" => false, "message" => "Invalid chatbot ID"], 400);
            }

            $configs = wpb_chatbot_resolve(ConfigsModel::class)->view($chatbotId, true);
            return new \WP_REST_Response(["configs" => $configs, "success" => true], 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], 500);
        }   
    }

    static function store(\WP_REST_Request $request) {
        $doc = $request->get_json_params();
        $chatbotId = $request->get_param('chatbot_id');

        if (empty($chatbotId)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid chatbot ID"], 400);
        }

        try {
            $validator = ConfigsValidator::make();

            if (!$validator->isValid($doc)) {
                return new \WP_REST_Response(["success" => false, "message" => $validator->getErrors()], 400);
            }

            $cleanDoc = $validator->getCleanData($doc);
            wpb_chatbot_resolve(ConfigsModel::class)->store($chatbotId,$cleanDoc);
            return new \WP_REST_Response(["success" => true], 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], $e->getCode());
        }
    }
}