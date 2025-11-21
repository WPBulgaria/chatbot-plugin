<?php

namespace WPBulgaria\Chatbot\Validators\Plan;

use WPBulgaria\Chatbot\Validators\BaseValidator;
use WPBulgaria\Chatbot\Enums\PlanPeriods;
use Ramsey\Uuid\Uuid;
use function WPBulgaria\Chatbot\Functions\validateDate;

defined( 'ABSPATH' ) || exit;

class PlanValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "id" => fn($data) => !$data || Uuid::isValid($data),
            "name" => fn($data) => !$data || is_string($data),
            "totalChats" => fn($data) => !$data || is_int($data),
            "totalQuestions" => fn($data) => !$data || is_int($data),
            "historySize" => fn($data) => !$data || is_int($data),
            "questionSize" => fn($data) => !$data || is_int($data),
            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
            "removedAt" => fn($data) => !$data || validateDate($data),
            "period" => fn($data) => !!PlanPeriods::tryFrom($data),
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