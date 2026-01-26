<?php

namespace WPBulgaria\Chatbot\Validators\Chatbot;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use WPBulgaria\Chatbot\Validators\Configs\ConfigsValidator;
defined('ABSPATH') || exit;

class ChatbotValidator extends BaseValidator {
    
    private ConfigsValidator $configValidator;

    public function __construct() {
        $this->configValidator = ConfigsValidator::make();
        
        $this->rules = [
            "title" => fn($data) => is_string($data) && strlen(trim($data)) > 0 && strlen($data) <= 200,
            "description" => fn($data) => !$data || is_string($data),
            "config" => fn($data) => $this->configValidator->isValid($data),
        ];

        $this->messages = [
            "title" => "Title is required and must be a non-empty string with max 200 characters",
            "description" => "Description must be a string",
            "config" => "Invalid chatbot configuration",
        ];
    }

    public function isValid($data): bool {
        if (empty($data)) {
            $this->errors["data"] = "No data provided";
            return false;
        }

        if (!is_array($data)) {
            $this->errors["data"] = "Invalid data format";
            return false;
        }

        return parent::isValid($data);
    }

    public function validateTitle(string $title): bool {
        return $this->isValidField("title", $title);
    }

    public function validateDescription(?string $description): bool {
        return $this->isValidField("description", $description);
    }

    public function validateConfig(?array $config): bool {
        $isValid = $this->configValidator->isValid($config ?? []);
        if (!$isValid) {
            $this->errors = array_merge($this->errors, $this->configValidator->getErrors());
            return false;
        }
        return true;
    }
}
