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

class GeminiService {

    protected $client;
    protected $configs;
    protected $model;

    public function __construct() {
        $this->configs = ConfigsModel::view();
    }

    /**
     * Get or create the Gemini client
     */
    public function getClient() {
        if ($this->client) {
            return $this->client;
        }

        if (empty($this->configs["apiKey"])) {
            return false;
        }

        $this->client = Gemini::client($this->configs["apiKey"]);
        return $this->client;
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool {
        return !empty($this->configs["apiKey"]);
    }

    /**
     * Get the configured model name
     */
    public function getModelName(): string {
        return $this->configs["model"] ?? "gemini-2.5-flash";
    }

    /**
     * Get system instructions from config
     */
    public function getSystemInstructions(): ?string {
        return $this->configs["systemInstructions"] ?? null;
    }

    /**
     * Get file search store name from config
     */
    protected function getFileSearchStore(): ?string {
        if (empty($this->configs["fileSearchStore"])) {
            return null;
        }

        try {
            return SearchFileModel::getFileSearchStore($this->configs["fileSearchStore"]);
        } catch (\Exception $e) {
            error_log("Failed to get file search store: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a configured generative model instance
     */
    public function createModel(bool $withGenerationConfig = false) {
        $client = $this->getClient();
        if (!$client) {
            throw new \Exception("API key not configured", 400);
        }

        $model = $client->generativeModel($this->getModelName());

        // Add generation config for streaming
        if ($withGenerationConfig) {
            $generateConfig = new GenerationConfig(
                temperature: 0.1,
                maxOutputTokens: 800,
                topP: 0.8,
                topK: 20,
                stopSequences: [],
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
            $fileSearchStore = $this->getFileSearchStore();
            if ($fileSearchStore) {
                $model->withTool(new Tool(
                    fileSearch: new FileSearch(fileSearchStoreNames: [$fileSearchStore]),
                ));
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
