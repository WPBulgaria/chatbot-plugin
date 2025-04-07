<?php

namespace ExpertsCrm\Actions;

use ExpertsCrm\DataObjects\Transactions\Touch\ListConfig;
use ExpertsCrm\Transactions\TouchTransaction;
use ExpertsCrm\Validators\Touch\TouchValidator;

defined( 'ABSPATH' ) || exit;

class TouchAction {

    static function list(\WP_REST_Request $request) {
        $pointer = (int) $request->get_param('pointer');
        $query = sanitize_text_field($request->get_param('query'));
        $sort = $request->get_param('sort') ?? "";

        $result = TouchTransaction::list(
            new ListConfig($pointer, $query, $sort)
        );
        return new \WP_REST_Response($result->toArray(), 200);
    }

    static function trash(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = sanitize_key( $data['id'] );

        $validator = TouchValidator::make();
        if (!$validator->isValidField("_id", $id)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }

        if (empty($id)) {
            return new \WP_REST_Response(["message" => "invalid data"], 400);
        }

        try {
            TouchTransaction::trash($id);
            return new \WP_REST_Response([], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["message" => $e->getMessage()], $e->getCode());
        }
    }


    static function view(\WP_REST_Request $request) {
        $data = $request->get_params();

        try {
            $id = sanitize_key( $data['id'] ?? "" );

            $validator = TouchValidator::make();
            if (!$validator->isValidField("_id", $id)) {
                return new \WP_REST_Response($validator->getErrors(), 400);
            }

            $doc = TouchTransaction::view($id);
            return new \WP_REST_Response($doc, 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["message" => $e->getMessage()], $e->getCode());
        }
    }

    static function store(\WP_REST_Request $request) {
        $urlParams = $request->get_url_params();
        //Validate doc
        $doc = $request->get_json_params();


        if (!is_array($doc)) {
            return new \WP_REST_Response(["message" => "invalid data"], 400);
        }

        try {
            $validator = TouchValidator::make();

            if (!$validator->isValid($doc)) {
                return new \WP_REST_Response($validator->getErrors(), 400);
            }

            $cleanDoc = $validator->getCleanData($doc);
            $id = TouchTransaction::store($cleanDoc, $urlParams["id"] ?? null);
            return new \WP_REST_Response(["opportunity" => $id], 200);
        } catch ( \Exception $e ) {
            return new \WP_Error(500, "Internal server error");
        }
    }
}