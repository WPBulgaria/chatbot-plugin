<?php

namespace WPBulgaria\Chatbot\Validators\Configs;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function WPBulgaria\Chatbot\Functions\validateDate;

defined( 'ABSPATH' ) || exit;

class ConsultantValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "systemInstructions" => fn($data) => !$data || is_string($data),
            "temperature" => fn($data) => is_numeric($data) && $data >= 0 && $data <= 2,
            "maxOutputTokens" => fn($data) => is_int($data) && $data > 0 && $data <= 65000,
            "topP" => fn($data) => is_numeric($data) && $data >= 0 && $data <= 1,
            "topK" => fn($data) => is_int($data) && $data > 0 && $data <= 100,
            "model" => fn($data) => is_string($data) && preg_match('/^[-a-z0-9_\/.]+$/', $data),
            "enabled" => fn($data) => is_bool($data),
            "description" => fn($data) => !$data || is_string($data) && mb_strlen($data) <= 200000,
        ];
    }

    function isValid($data): bool
    {

        if (isset($data["enabled"]) && !$data["enabled"]) {
            return true;
        }

        if (!is_array($data)) {
            $this->errors["action"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}