<?php

namespace WPBulgaria\Chatbot\Transactions\File;

use WPBulgaria\Chatbot\Models\SearchFileModel;
use WPBulgaria\Chatbot\Models\FileModel;


defined( 'ABSPATH' ) || exit;

class FileUseTransaction {
    protected SearchFileModel $searchFileModel;
    protected FileModel $fileModel;

    public function __construct(SearchFileModel $searchFileModel, FileModel $fileModel) {
        $this->searchFileModel = $searchFileModel;
        $this->fileModel = $fileModel;
    }

    public function execute(int $id) {
        $attachment = get_post($id);
        if (!$attachment) {
            throw new \Exception("File not found");
        }

        $chatbotId = $attachment->post_parent;

        $filesInUse = get_post_meta($attachment->post_parent, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
        if (empty($filesInUse)) {
            $filesInUse = [];
        } else if (in_array($chatbotId, $filesInUse)) {
            throw new \Exception("Chatbot already has this file in use");
        }


        try {
            $this->searchFileModel->upload($attachment);
            $filesInUse[] = $chatbotId;
            update_post_meta($id, WPB_CHATBOT_FILE_IN_USE_FIELD, $filesInUse);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to upload file to Gemini: " . $e->getMessage());
        }

    }
}
