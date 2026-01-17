<?php

namespace WPBulgaria\Chatbot\Validators\Chat;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use function WPBulgaria\Chatbot\Functions\validateDate;

defined( 'ABSPATH' ) || exit;

class ChatValidator extends BaseValidator {
    
    function __construct() {
        $this->rules = [
            "message" => fn($data) => is_string($data) && strlen(trim($data)) > 0,
            "chatId" => fn($data) => !$data || (is_int($data) && $data > 0),
            "title" => fn($data) => !$data || (is_string($data) && strlen(trim($data)) > 0 && strlen($data) <= 200),
            "userId" => fn($data) => !$data || (is_int($data) && $data >= 0),
        ];

        $this->messages = [
            "message" => "Message is required and must be a non-empty string",
            "chatId" => "Chat ID must be a positive integer",
            "title" => "Title must be a non-empty string with max 200 characters",
            "userId" => "User ID must be a non-negative integer",
        ];
    }

    function isValid($data): bool {
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

    public function validateMessage(string $message): bool {
        return $this->isValidField("message", $message);
    }

    public function validateChatId($chatId): bool {
        if ($chatId === null) {
            return true;
        }
        return $this->isValidField("chatId", $chatId);
    }

    public function validateTitle(string $title): bool {
        return $this->isValidField("title", $title);
    }
}