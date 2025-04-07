<?php

namespace ExpertsCrm\Actions;

use ExpertsCrm\DataObjects\Models\List\ListConfig;
use ExpertsCrm\DataObjects\Transactions\List\ViewConfig;
use ExpertsCrm\Transactions\ListTransaction;
use ExpertsCrm\Validators\List\ListValidator;

defined( 'ABSPATH' ) || exit;

class ListAction {

    static function list(\WP_REST_Request $request) {
        $pointer = (int) $request->get_param('pointer');
        $query = sanitize_text_field($request->get_param('query'));
        $sort = $request->get_param('sort') ?? "";
        $expanded = !empty($request->get_param('expanded'));


        $result = ListTransaction::list(
            new ListConfig($pointer, $query, $expanded, $sort)
        );
        return new \WP_REST_Response($result->toArray(), 200);
    }

    static function trash(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = sanitize_key( $data['id'] );

        $validator = ListValidator::make();
        if (!$validator->isValidField("_id", $id)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }


        try {
            ListTransaction::trash($id);
            return new \WP_REST_Response([], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["message" => $e->getMessage()], $e->getCode());
        }

    }


    static function view(\WP_REST_Request $request) {
        $data = $request->get_params();

        try {
            $id = sanitize_key( $data['id'] ?? "" );
            $expanded = !empty($data['expanded']);
            $validator = ListValidator::make();
            if (!$validator->isValidField("_id", $id)) {
                return new \WP_REST_Response($validator->getErrors(), 400);
            }


            $doc = ListTransaction::view(new ViewConfig($id, $expanded));
            return new \WP_REST_Response($doc, 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["message" => $e->getMessage()], $e->getCode());
        }
    }

    static function store(\WP_REST_Request $request) {
        $urlParams = $request->get_url_params();
        //Validate doc
        $doc = $request->get_json_params();

        $validator = ListValidator::make();
        if (!$validator->isValid($doc)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }


        try {
            $cleanDoc = $validator->getCleanData($doc);
            $id = ListTransaction::store($cleanDoc, $urlParams["id"] ?? null);
            return new \WP_REST_Response(["list" => $id], 200);
        } catch ( \Exception $e ) {
            return new \WP_Error(500, "Internal server error");
        }

    }
}