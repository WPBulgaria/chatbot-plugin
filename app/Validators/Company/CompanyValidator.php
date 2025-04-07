<?php

namespace ExpertsCrm\Validators\Company;
use ExpertsCrm\Validators\Action\ActionListValidator;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class CompanyValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "uin" => fn($data) => !$data || is_string($data),
            "vat" => fn($data) => !$data || is_string($data),
            "name" => fn($data) => !$data || is_string($data),
            "mol" => fn($data) => !$data || is_string($data),
            "phone" => fn($data) => !$data || is_string($data),
            "email" => fn($data) => !$data || filter_var($data, FILTER_VALIDATE_EMAIL),
            "website" => fn($data) => !$data || filter_var($data, FILTER_VALIDATE_URL),
            "actions" => ActionListValidator::make(),
            "province" => fn($data) => !$data || is_string($data),
            "city" => fn($data) => !$data || is_string($data),
            "country" => fn($data) => !$data || is_string($data),
            "postalCode" => fn($data) => !$data || is_string($data),
            "address" => fn($data) => !$data || is_string($data),

            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
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