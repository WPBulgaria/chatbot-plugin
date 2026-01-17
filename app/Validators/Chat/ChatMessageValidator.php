<?php
namespace WPBulgaria\Chatbot\Validators\Chat;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use function WPBulgaria\Chatbot\Functions\validateDate;

defined( 'ABSPATH' ) || exit;

class ChatMessageValidator extends BaseValidator {

function __construct() {
    $this->rules = [
        "role" => fn($data) => in_array($data, ['user', 'model', 'system']),
        "content" => fn($data) => is_string($data) && strlen($data) > 0,
        "createdAt" => fn($data) => !$data || validateDate($data),
    ];

    $this->messages = [
        "role" => "Role must be 'user', 'model', or 'system'",
        "content" => "Content is required and must be a non-empty string",
        "createdAt" => "Invalid date format",
    ];
}

function isValid($data): bool {
    if (empty($data)) {
        $this->errors["data"] = "Message data is required";
        return false;
    }

    if (!is_array($data)) {
        $this->errors["data"] = "Invalid message format";
        return false;
    }

    if (!isset($data['role'])) {
        $this->errors["role"] = "Role is required";
        return false;
    }

    if (!isset($data['content'])) {
        $this->errors["content"] = "Content is required";
        return false;
    }

    return parent::isValid($data);
}
}
