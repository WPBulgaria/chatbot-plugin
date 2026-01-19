<?php

namespace WPBulgaria\Chatbot\Models;


use WPBulgaria\Chatbot\Services\GeminiService;

defined( 'ABSPATH' ) || exit;

class SearchFileModel {

    protected GeminiService $geminiService;
    protected ConfigsModel $configsModel;

    public function __construct(GeminiService $geminiService, ConfigsModel $configsModel) {
        $this->geminiService = $geminiService;
        $this->configsModel = $configsModel;
    }

    public function listFileSearchStores() {
        return $this->geminiService->listFileSearchStores();
    }

    public function getFileSearchStore(string $name)
    {
        return $this->geminiService->getFileSearchStore($name);
    }   

    public function upload(\WP_Post $attachment) {
        $configs = $this->configsModel->view();
    
        if (empty($configs["fileSearchStore"])) {
            throw new \Exception("File Search Store not configured");
        }


        $fileSearchStore = $this->getFileSearchStore($configs["fileSearchStore"]);
        if (!$fileSearchStore) {
            throw new \Exception("File Search Store not found");
        }

        $path = get_attached_file($attachment->ID);
        $client = $this->geminiService->getClient();

        $response = $client->fileSearchStores()->upload(
            storeName: $fileSearchStore,
            filename: $path,
            displayName: $attachment->guid,
        );

        return $response->documentName;
    }

    public function remove(string $guid) {
        $configs = $this->configsModel->view();
    
        if (empty($configs["fileSearchStore"])) {
            throw new \Exception("File Search Store not configured");
        }

        $fileSearchStore = $this->getFileSearchStore($configs["fileSearchStore"]);

        if (!$fileSearchStore) {
            throw new \Exception("File Search Store not found");
        }

        $client = $this->geminiService->getClient();

        if (!$client) {
            throw new \Exception("Gemini client not found");
        }

        try {
            $files = $client->fileSearchStores()->listDocuments($fileSearchStore);
            foreach ($files->documents as $file) {
                if ($file->displayName === $guid) {
                    $client->fileSearchStores()->deleteDocument($file->name, true);
                    break;
                }
            }   
        } catch (\Exception $e) {
            throw new \Exception("Failed to remove file from Gemini: " . $e->getMessage());
        }

        return true;
    }
}