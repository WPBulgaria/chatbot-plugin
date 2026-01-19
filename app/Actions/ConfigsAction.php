<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Validators\Configs\ConfigsValidator;

defined( 'ABSPATH' ) || exit;

class ConfigsAction {

    static function view() {
        try {
            $configs = wpb_chatbot_resolve(ConfigsModel::class)->view(true);
            return new \WP_REST_Response($configs, 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], 500);
        }   
    }

    static function store(\WP_REST_Request $request) {
        $doc = $request->get_json_params();

        if (!is_array($doc)) {  
            return new \WP_REST_Response(["success" => false, "message" => "invalid data"], 400);
        }

        try {
            $validator = ConfigsValidator::make();

            if (!$validator->isValid($doc)) {
                return new \WP_REST_Response(["success" => false, "message" => $validator->getErrors()], 400);
            }

            $cleanDoc = $validator->getCleanData($doc);
            wpb_chatbot_resolve(ConfigsModel::class)->store($cleanDoc);
            return new \WP_REST_Response(["success" => true], 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], $e->getCode());
        }
    }
}