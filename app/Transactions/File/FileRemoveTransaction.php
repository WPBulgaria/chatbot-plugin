<?php

namespace WPBulgaria\Chatbot\Transactions\File;

use WPBulgaria\Chatbot\Models\FileModel;
use WPBulgaria\Chatbot\Models\SearchFileModel;

defined( 'ABSPATH' ) || exit;

class FileRemoveTransaction {
    protected SearchFileModel $searchFileModel;
    protected FileModel $fileModel;

    public function __construct(SearchFileModel $searchFileModel, FileModel $fileModel) {
        $this->searchFileModel = $searchFileModel;
        $this->fileModel = $fileModel;
    }

    public function execute(int $id) {
        $fileInUse = get_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, true) ?: [];
        $attachment = get_post($id);
        if (is_array($fileInUse) && in_array($attachment->post_parent, $fileInUse)) {
            $this->searchFileModel->remove($attachment->guid, $attachment->post_parent);
            $fileInUse = array_diff($fileInUse, [$attachment->post_parent]);
            update_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, $fileInUse);
        }
        return $this->fileModel->remove($id);
    }
}   