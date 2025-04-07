<?php

namespace ExpertsCrm\Validators\People;
use ExpertsCrm\Enums\Flags;
use ExpertsCrm\Enums\Phases;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class StoreValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "listId" => fn($data) => !$data || Uuid::isValid($data),
            "companyId" => fn($data) => !$data || Uuid::isValid($data),
            "role" => fn($data) => !$data || is_string($data),
            "name" => fn($data) => !$data || is_string($data),

            "flag" => fn($data) => !!Flags::tryFrom($data),
            "phase" => fn($data) => !!Phases::tryFrom($data),

            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
            "addresses" => fn($data) => AddressListValidator::make(),
            "contacts" => fn($data) => ContactValidator::make(),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["company"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}