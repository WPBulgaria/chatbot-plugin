<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\FileModel;
use WPBulgaria\Chatbot\Transactions\File\FileRemoveTransaction;
use WPBulgaria\Chatbot\Transactions\File\FileUseTransaction;

defined( 'ABSPATH' ) || exit;

class FileAction {

    static function upload(\WP_REST_Request $request) {
        $files = $request->get_file_params();

        if (empty($files['file'])) {
            return new \WP_REST_Response(["success" => false, "message" => "No file provided"], 400);
        }

        try {
            $file = wpb_chatbot_app(FileModel::class)->upload($files['file']);
            return new \WP_REST_Response(["success" => true, "file" => $file], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["success" => false, "message" => esc_html($e->getMessage())], $e->getCode() ?: 500);
        }
    }

    static function remove(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = isset($data['id']) ? absint($data['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid file ID"], 400);
        }

        try {
            wpb_chatbot_app(FileRemoveTransaction::class)->execute($id);
            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["success" => false, "message" => esc_html($e->getMessage())], $e->getCode() ?: 500);
        }
    }

    static function list(\WP_REST_Request $request) {
        $params = $request->get_params();
        $per_page = isset($params['per_page']) ? absint($params['per_page']) : 20;
        $page = isset($params['page']) ? absint($params['page']) : 1;

        $result = wpb_chatbot_app(FileModel::class)->list($per_page, $page);

        return new \WP_REST_Response([
            "success" => true,
            "files"   => $result['files'],
            "total"   => $result['total'],
            "pages"   => $result['pages']
        ], 200);
    }

    static function use(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = isset($data['id']) ? absint($data['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid file ID"], 400);
        }

        $file = wpb_chatbot_app(FileModel::class)->get($id);
        if (!$file) {
            return new \WP_REST_Response(["success" => false, "message" => "File not found"], 404);
        }

        $inUse = get_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
        if ($inUse === '1') {
            return new \WP_REST_Response(["success" => false, "message" => "File already in use"], 400);
        }
        
        try {
            wpb_chatbot_app(FileUseTransaction::class)->execute($id);
            return new \WP_REST_Response(["success" => true], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response(["success" => false, "message" => esc_html($e->getMessage())], $e->getCode() ?: 500);
        }
    }
}
