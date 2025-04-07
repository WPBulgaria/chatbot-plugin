<?php

namespace ExpertsCrm\Validators\People;
use ExpertsCrm\Enums\ContactTypes;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class ContactValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "content" => fn($data) => is_string($data),
            "subtype" => fn($data) => !!ContactTypes::tryFrom($data),

            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["contact"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}