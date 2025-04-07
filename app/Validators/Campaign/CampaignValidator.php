<?php

namespace ExpertsCrm\Validators\Campaign;
use ExpertsCrm\Enums\ObjectTypes;
use ExpertsCrm\Enums\Priority;
use ExpertsCrm\Enums\Statuses;
use ExpertsCrm\Validators\Action\ActionListValidator;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class CampaignValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "name" => fn($data) => !$data || is_string($data),
            "description" => fn($data) => !$data || is_string($data),
            "color" => fn($data) => !$data || is_string($data),
            "priority" => fn($data) => Priority::tryFrom($data),

            "createdAt" => fn($data) => validateDate($data),
            "modifiedAt" => fn($data) => validateDate($data),


            "budgetedValue" => fn($data) => !$data || is_numeric($data),
            "actualValue" => fn($data) => !$data || is_numeric($data),
            "projectedReach" => fn($data) => !$data || is_numeric($data),
            "actualReach" => fn($data) => !$data || is_numeric($data),

            "objectId" => fn($data) => !$data || Uuid::isValid($data),
            "objectType" => fn($data) => !$data || !!ObjectTypes::tryFrom($data),

            "indicator" => fn($data) => is_int($data),
            "status" => fn($data) => !!Statuses::tryFrom($data),
            "actions" => ActionListValidator::make(),
        ];
    }

    function isValid($data): bool
    {
        if (!is_array($data)) {
            $this->errors["campaign"] = "Invalid document";
            return false;
        }

        return parent::isValid($data);
    }
}