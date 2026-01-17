<?php

namespace WPBulgaria\Chatbot\Transactions\File;

use WPBulgaria\Chatbot\Models\FileModel;
use WPBulgaria\Chatbot\Models\SearchFileModel;

defined( 'ABSPATH' ) || exit;

class FileRemoveTransaction {
    static function execute(int $id) {
        $fileInUse = get_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
        $attachment = get_post($id);
        if ($fileInUse === '1') {
            SearchFileModel::remove($attachment->guid);
            delete_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD);
        }
        return FileModel::remove($id);
    }
}   