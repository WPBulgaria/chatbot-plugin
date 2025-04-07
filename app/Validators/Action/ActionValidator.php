<?php

namespace ExpertsCrm\Validators\Action;
use ExpertsCrm\Enums\ActionTypes;
use ExpertsCrm\Enums\ObjectTypes;
use ExpertsCrm\Enums\SchedulePeriods;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class ActionValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "_id" => fn($data) => !$data || Uuid::isValid($data),
            "ownerId" => fn($data) => !$data || Uuid::isValid($data),
            "type" => fn($data) => !!ActionTypes::tryFrom($data),

            "objectId" => fn($data) => !$data || Uuid::isValid($data),
            "objectType" => fn($data) => !!ObjectTypes::tryFrom($data),


            "period" => fn($data) => !!SchedulePeriods::tryFrom($data),
            "startsAt" => fn($data) => !$data || validateDate($data),
            "finishesAt" => fn($data) => !$data || validateDate($data),
            "completedAt" => fn($data) => !$data || validateDate($data),
            "repeatUntil" => fn($data) => !$data || validateDate($data),
            "viewedAt" => fn($data) => !$data || validateDate($data),
            "createdAt" => fn($data) => !$data || validateDate($data),
            "modifiedAt" => fn($data) => !$data || validateDate($data),
            "allDay" => fn($data) => $data === null || is_bool($data),
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