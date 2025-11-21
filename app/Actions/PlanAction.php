<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Validators\Plan\PlanValidator;
use WPBulgaria\Chatbot\Models\PlanModel;

defined( 'ABSPATH' ) || exit;

class PlanAction {

    static function list() {
        $result = PlanModel::list();
        return new \WP_REST_Response($result, 200);
    }

    static function trash(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = sanitize_key( $data['id'] );

        $validator = PlanValidator::make();
        if (!$validator->isValidField("id", $id)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "invalid data"], 400);
        }

        try {
            PlanModel::trash($id);
            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], $e->getCode());
        }
    }


    static function store(\WP_REST_Request $request) {
        $urlParams = $request->get_url_params();
        //Validate doc
        $doc = $request->get_json_params();

        if (!is_array($doc)) {  
            return new \WP_REST_Response(["success" => false, "message" => "invalid data"], 400);
        }

        try {
            $validator = PlanValidator::make();

            if (!$validator->isValid($doc)) {
                return new \WP_REST_Response(["success" => false, "message" => $validator->getErrors()], 400);
            }

            $cleanDoc = $validator->getCleanData($doc);
            $id = PlanModel::store($cleanDoc, $urlParams["id"] ?? null);
            return new \WP_REST_Response(["success" => true, "plan" => $id], 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["success" => false, "message" => "Internal server error"], $e->getCode());
        }
    }
}