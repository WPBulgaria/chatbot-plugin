<?php

namespace WPBulgaria\Chatbot\Models;

defined('ABSPATH') || exit;

/**
 * Configuration Model - handles plugin settings
 * Supports both static methods (backward compatibility) and instance methods (DI)
 */
class ConfigsModel {

    const OPTIONS_KEY = "wpb_chatbot_configs";
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
    ];

    protected OptionModel $optionModel;

    public function __construct(OptionModel $optionModel) {
        $this->optionModel = $optionModel;
    }

    /**
     * Cached config for instance methods
     */
    private ?array $cachedConfig = null;

    /**
     * Get config (instance method for DI)
     */
    public function view(bool $secure = false): array {
        if ($this->cachedConfig === null || $secure) {
            $configs = $this->optionModel->get(self::OPTIONS_KEY, []);

            if (empty($configs)) {
                return self::DEFAULT_CONFIGS;
            }
    
            if ($secure) {
                $configs["apiKey"] = self::KEY_MASK;
            }

            $this->cachedConfig = $configs;
        }
        return $this->cachedConfig;
    }

    /**
     * Save config (instance method for DI)
     */
    public function store(array $doc): bool {
        $configs = $this->optionModel->get(self::OPTIONS_KEY, []);

        if (empty($configs)) {
            $configs = self::DEFAULT_CONFIGS;
            $configs["modifiedAt"] = date(DATE_ATOM);
        } else {
            $configs["modifiedAt"] = date(DATE_ATOM);
        }

        if (isset($doc["apiKey"]) && $doc["apiKey"] === self::KEY_MASK) {
            $doc["apiKey"] = $configs["apiKey"] ?? "";
        }

        $newDoc = array_merge($configs, $doc);
        $this->optionModel->update(self::OPTIONS_KEY, $newDoc);



        $this->cachedConfig = null; // Clear cache
        return true;
    }

    /**
     * Clear the cached config
     */
    public function clearCache(): void {
        $this->cachedConfig = null;
    }
}
