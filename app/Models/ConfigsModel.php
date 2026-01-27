<?php

namespace WPBulgaria\Chatbot\Models;

use WPBulgaria\Chatbot\Models\ChatbotModel;

defined('ABSPATH') || exit;

/**
 * Configuration Model - handles plugin settings
 * Supports both static methods (backward compatibility) and instance methods (DI)
 */
class ConfigsModel {

    const OPTIONS_KEY = ChatbotModel::META_CONFIG;
    const KEY_MASK = "🔒";

    const DEFAULT_CONFIGS = [
        "api_key" => "",
        "totalChats" => 100,
        "totalQuestions" => 100,
        "adminsOnly" => true,
        "publicPlan" => "",
        "defaultPlan" => "",
        "createdAt" => "",
        "modifiedAt" => "",
        "systemInstruction" => "",
        "chatTheme" => "",
        "temperature" => 0.1,
        "maxOutputTokens" => 800,
        "topP" => 0.8,
        "topK" => 20,
        "stopSequences" => [],
        "windowSize" => 10,
    ];


    protected PostModel $postModel;

    public function __construct(PostModel $postModel) {
        $this->postModel = $postModel;
    }

    /**
     * Cached config for instance methods
     */
    private ?array $cachedConfig = null;

    /**
     * Get config (instance method for DI)
     */
    public function view(int|string $chatbotId, bool $secure = false): array {
        if ($this->cachedConfig === null || $secure) {
            $configs = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY);

            if ($secure && is_array($configs)) {
                $configs["apiKey"] = self::KEY_MASK;
            }

            $this->cachedConfig = is_array($configs) ? $configs : [$configs];
        }
        return $this->cachedConfig;
    }

    /**
     * Save config (instance method for DI)
     */
    public function store(int|string $chatbotId, array $doc): bool {
        $configs = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY) ?? [];

        if (empty($configs)) {
            $configs = [];
        }

        if (isset($doc["apiKey"]) && !empty($doc["apiKey"]) && $doc["apiKey"] === self::KEY_MASK) {
            $doc["apiKey"] = $configs["apiKey"] ?? "";
        }

        $newDoc = array_merge($configs, $doc);
        $normalizedDoc = $this->normalizeConfig($newDoc);

        $this->postModel->updateMeta($chatbotId, self::OPTIONS_KEY, $normalizedDoc);
        $this->cachedConfig = null; // Clear cache
        return true;
    }

    /**
     * Clear the cached config
     */
    public function clearCache(): void {
        $this->cachedConfig = null;
    }

    
    /**
     * Normalize config with defaults
     */
    private function normalizeConfig(array $config): array {
        return array_merge(self::DEFAULT_CONFIGS, $config);
    }
}
