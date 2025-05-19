<?php

namespace ExpertsCrm\Validators\Touch;
use ExpertsCrm\Enums\ObjectTypes;
use ExpertsCrm\Validators\Action\ActionListValidator;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class TouchValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "title" => fn($data) => !$data || is_string($data),
            "createdAt" => fn($data) => validateDate($data),
            "modifiedAt" => fn($data) => validateDate($data),

            "actions" => ActionListValidator::make(),

            "objectId" => fn($data) => !$data || Uuid::isValid($data),
            "objectType" => fn($data) => !$data || !!ObjectTypes::tryFrom($data),
            "subtype" => fn($data) => !$data || is_string($data),

            "content" => fn($data) => !$data || is_string($data),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["touch"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}