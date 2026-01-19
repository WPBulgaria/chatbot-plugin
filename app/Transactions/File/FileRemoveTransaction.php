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
        $fileInUse = get_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
        $attachment = get_post($id);
        if ($fileInUse === '1') {
            $this->searchFileModel->remove($attachment->guid);
            delete_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD);
        }
        return $this->fileModel->remove($id);
    }
}   