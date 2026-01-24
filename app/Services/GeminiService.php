<?php

namespace WPBulgaria\Chatbot\Services;

use Gemini;
use Gemini\Data\Content;
use Gemini\Data\FileSearch;
use Gemini\Data\Tool;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\Role;
use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Models\SearchFileModel;

defined('ABSPATH') || exit;

/**
 * Gemini API Service - handles all Gemini API interactions
 */
class GeminiService {

    private ?object $client = null;
    private ConfigsModel $configsModel;
    private ?array $configs = null;

    /**
     * Constructor with dependency injection
     */
    public function __construct(ConfigsModel $configsModel) {
        $this->configsModel = $configsModel;
    }

    /**
     * Get configs (lazy loaded)
     */
    protected function getConfigs(): array {
        if ($this->configs === null) {
            $this->configs = $this->configsModel->view();
        }
        return $this->configs;
    }

    /**
     * Get or create the Gemini client
     */
    public function getClient(): object|false {
        if ($this->client !== null) {
            return $this->client;
        }

        $configs = $this->getConfigs();

        if (empty($configs["apiKey"])) {
            return false;
        }

        $this->client = Gemini::client($configs["apiKey"]);
        return $this->client;
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool {
        $configs = $this->getConfigs();
        return !empty($configs["apiKey"]);
    }

    /**
     * Get the configured model name
     */
    public function getModelName(): string {
        $configs = $this->getConfigs();
        return $configs["model"] ?? "gemini-2.5-flash";
    }

    /**
     * Get system instructions from config
     */
    public function getSystemInstructions(): ?string {
        $configs = $this->getConfigs();
        return $configs["systemInstructions"] ?? null;
    }

    public function listFileSearchStores(): array {
        $client = $this->getClient();
        if (!$this->isConfigured() || !$client) {
            throw new \Exception("Gemini client not found");    
        }

        try {
            $response = $client->fileSearchStores()->list();
            return $response->fileSearchStores;
        } catch (\Exception $e) {
            throw new \Exception("Failed to list file search stores: " . $e->getMessage());
        }
    }

    /**
     * Get file search store name from config
     */
    public function getFileSearchStore(string $name): ?string {
        $client = $this->getClient();
        if (!$client || empty($name)) {
            throw new \Exception("Gemini client not found");
        }

        try {
            $stores = $this->listFileSearchStores();
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

    /**
     * Create a configured generative model instance
     */
    public function createModel(bool $withGenerationConfig = false): object {
        $client = $this->getClient();
        if (!$client) {
            throw new \Exception("API key not configured", 400);
        }

        $model = $client->generativeModel($this->getModelName());
        $configs = $this->getConfigs();

        // Add generation config for streaming
        if ($withGenerationConfig) {
            $generateConfig = new GenerationConfig(
                temperature: $configs["temperature"] ?? 0.1,
                maxOutputTokens: $configs["maxOutputTokens"] ?? 800,
                topP: $configs["topP"] ?? 0.8,
                topK: $configs["topK"] ?? 20,
                stopSequences: $configs["stopSequences"] ?? [],
            );
            $model->withGenerationConfig($generateConfig);
        }

        // Add system instructions
        $systemInstructions = $this->getSystemInstructions();
        if (!empty($systemInstructions)) {
            $model->withSystemInstruction(Content::parse(part: $systemInstructions));
        }

        // Add file search tool
        try {
            $configs = $this->getConfigs();
            if (!empty($configs["fileSearchStore"])) {  
                $fileSearchStore = $this->getFileSearchStore($configs["fileSearchStore"]);
                if ($fileSearchStore) {
                    $model->withTool(new Tool(
                        fileSearch: new FileSearch(fileSearchStoreNames: [$fileSearchStore]),
                    ));
                }
            }
        } catch (\Exception $e) {
            error_log("Failed to add file search store to model: " . $e->getMessage());
        }

        return $model;
    }

    /**
     * Build chat history for Gemini from messages array
     */
    public function buildHistory(array $messages): array {
        $history = [];
        $messagesToProcess = array_slice($messages, 0, -1);

        foreach ($messagesToProcess as $msg) {
            $history[] = Content::parse(
                part: $msg["content"],
                role: $msg["role"] === 'model' ? Role::MODEL : Role::USER
            );
        }

        return $history;
    }

    /**
     * Send a message and get response
     */
    public function sendMessage(string $message, array $history): string {
        $model = $this->createModel();
        $response = $model->startChat(history: $history)->sendMessage($message);
        return $response->text();
    }

    /**
     * Send a message and stream response
     */
    public function streamMessage(string $message, array $history, callable $onChunk): string {
        $model = $this->createModel(withGenerationConfig: true);
        $stream = $model->startChat(history: $history)->streamSendMessage($message);

        session_write_close();
        $responseText = '';

        foreach ($stream as $chunk) {
            $chunkText = '';

            if ($chunk->candidates && count($chunk->candidates) > 0) {
                foreach ($chunk->parts() as $part) {
                    if (!empty($part->text)) {
                        $chunkText .= $part->text;
                        $responseText .= $part->text;
                    }
                }
            }

            if (empty($chunkText)) {
                continue;
            }

            $onChunk($chunkText);
        }

        return $responseText;
    }

    /**
     * Create a new message array entry
     */
    public static function createMessage(string $role, string $content): array {
        return [
            'role'      => $role,
            'content'   => $content,
            'createdAt' => date(DATE_ATOM),
        ];
    }
}
