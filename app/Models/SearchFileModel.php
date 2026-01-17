<?php

namespace WPBulgaria\Chatbot\Models;

use Gemini\Enums\ModelVariation;
use Gemini\GeminiHelper;
use Gemini;
use Gemini\Enums\FileState;
use Gemini\Enums\MimeType;
use Gemini\Enums\Schema;
use Gemini\Enums\DataType;


defined( 'ABSPATH' ) || exit;

class SearchFileModel {
    static function getFileSearchStoreClient() {
        $configs = ConfigsModel::view();
        if (empty($configs["apiKey"])) {
            return false;
        }

        return Gemini::client($configs["apiKey"]);
    }

    static function listFileSearchStores() {
        $client = self::getFileSearchStoreClient();
        if (!$client) {
            throw new \Exception("Gemini client not found");    
        }

        try {
            $response = $client->fileSearchStores()->list();
            return $response->fileSearchStores;
        } catch (\Exception $e) {
            throw new \Exception("Failed to list file search stores: " . $e->getMessage());
        }
    }

    static function getFileSearchStore(string $name)
    {
        $client = self::getFileSearchStoreClient();
        if (!$client) {
            throw new \Exception("Gemini client not found");
        }

        try {
            $stores = self::listFileSearchStores();
            foreach ($stores as $store) {
                if ($store->displayName === $name) {
                    return $store->name;
                }
            }

            $response = $client->fileSearchStores()->create(
                displayName: $name
            );
            
            return $response->name;
        
        } catch (\Exception $e) {
            throw new \Exception("Failed to get file search store: " . $e->getMessage());
        }
    }   

    static function upload(\WP_Post $attachment) {
        $configs = ConfigsModel::view();
    
        if (empty($configs["fileSearchStore"])) {
            throw new \Exception("File Search Store not configured");
        }


        $fileSearchStore = self::getFileSearchStore($configs["fileSearchStore"]);
        if (!$fileSearchStore) {
            throw new \Exception("File Search Store not found");
        }

        $path = get_attached_file($attachment->ID);
        $client = self::getFileSearchStoreClient();

        $response = $client->fileSearchStores()->upload(
            storeName: $fileSearchStore,
            filename: $path,
            displayName: $attachment->guid,
        );

        return $response->documentName;
    }

    static function remove(string $guid) {
        $configs = ConfigsModel::view();
    
        if (empty($configs["fileSearchStore"])) {
            throw new \Exception("File Search Store not configured");
        }

        $fileSearchStore = self::getFileSearchStore($configs["fileSearchStore"]);

        if (!$fileSearchStore) {
            throw new \Exception("File Search Store not found");
        }

        $client = self::getFileSearchStoreClient();

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