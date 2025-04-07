<?php

namespace ExpertsCrm\Validators\People;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class AddressValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),

            "country" => fn($data) => !$data || is_string($data),
            "province" => fn($data) => !$data || is_string($data),
            "city" => fn($data) => !$data || is_string($data),
            "postalCode" => fn($data) => !$data || is_string($data),
            "address" => fn($data) => !$data || is_string($data),

            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["address"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}