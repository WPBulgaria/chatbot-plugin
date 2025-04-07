<?php

namespace ExpertsCrm\Actions;

use ExpertsCrm\DataObjects\Models\People\ListConfig;
use ExpertsCrm\DataObjects\Transactions\People\ViewConfig;
use ExpertsCrm\Transactions\PeopleTransaction;
use ExpertsCrm\Validators\People\ListValidator;
use ExpertsCrm\Validators\People\StoreValidator;

defined( 'ABSPATH' ) || exit;

class PeopleAction {

    static function list(\WP_REST_Request $request) {

        $data = $request->get_params();
        $listData['pointer'] = (int) ($data['pointer'] ?? 0);
        $listData['query'] = sanitize_text_field($data['query'] ?? "");
        $listData["listId"] = sanitize_key( $data['list_id'] ?? null );
        $listData['expanded'] = !empty($request->get_param('expanded'));
        $listData["sort"] = sanitize_key( $data['sort'] ?? "" );

        $validator = ListValidator::make();
        if (!$validator->isValid($listData)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }

        $result = PeopleTransaction::list(
            $validator->getConfigObject()
        );

        return new \WP_REST_Response($result->toArray(), 200);
    }

    static function trash(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = sanitize_key( $data['id'] );

        $validator = StoreValidator::make();
        if (!$validator->isValidField("_id", $id)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }
        try {
            PeopleTransaction::trash($id);
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

            $validator = StoreValidator::make();
            if (!$validator->isValidField("_id", $id)) {
                return new \WP_REST_Response($validator->getErrors(), 400);
            }

            $doc = PeopleTransaction::view(new ViewConfig($id, $expanded));
            return new \WP_REST_Response( $doc, 200);
        } catch ( \Exception $e ) {
            return new \WP_REST_Response(["message" => $e->getMessage()], $e->getCode());
        }
    }

    static function store(\WP_REST_Request $request) {
        $urlParams = $request->get_url_params();
        //Validate doc
        $doc = $request->get_json_params();
        $validator = StoreValidator::make();
        if (!$validator->isValid($doc)) {
            return new \WP_REST_Response($validator->getErrors(), 400);
        }

        try {
            $cleanDoc = $validator->getCleanData($doc);
            $id = PeopleTransaction::store($cleanDoc, $urlParams["id"] ?? null);
            return new \WP_REST_Response(["person" => $id], 200);
        } catch ( \Exception $e ) {
            return new \WP_Error(500, "Internal server error");
        }
    }
}