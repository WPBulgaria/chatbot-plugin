<?php

namespace ExpertsCrm\Validators\List;
use ExpertsCrm\Enums\ListSources;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class ListValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "name" => fn($data) => is_string($data),
            "source" => fn($data) => !!ListSources::tryFrom($data),
            "options" => fn($data) => is_array($data),

            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["list"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}