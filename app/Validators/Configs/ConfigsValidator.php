<?php

namespace WPBulgaria\Chatbot\Validators\Configs;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function WPBulgaria\Chatbot\Functions\validateDate;

defined( 'ABSPATH' ) || exit;

class ConfigsValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "apiKey" => fn($data) => !$data || is_string($data),
            "fileSearchStore" => fn($data) => is_string($data) && preg_match('/^[-a-z0-9]+$/', $data),
            "totalChats" => fn($data) => !$data || is_int($data),
            "totalQuestions" => fn($data) => !$data || is_int($data),
            "adminsOnly" => fn($data) => $data === null || is_bool($data),
            "publicPlan" => fn($data) => !$data || Uuid::isValid($data),
            "defaultPlan" => fn($data) => !$data || Uuid::isValid($data),
            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),

        ];
    }

    function isValid($data): bool
    {
        if (empty($data)) {
            return true;
        }

        if (!is_array($data)) {
            $this->errors["action"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}