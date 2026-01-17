<?php

namespace WPBulgaria\Chatbot\Transactions\File;

use WPBulgaria\Chatbot\Models\SearchFileModel;


defined( 'ABSPATH' ) || exit;

class FileUseTransaction {
    static function execute(int $id) {
        $attachment = get_post($id);
        if (!$attachment) {
            throw new \Exception("File not found");
        }

        try {
            $result = SearchFileModel::upload($attachment);
            update_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, 1);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to upload file to Gemini: " . $e->getMessage());
        }

    }
}
