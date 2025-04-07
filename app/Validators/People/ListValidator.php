<?php

namespace ExpertsCrm\Validators\People;
use ExpertsCrm\DataObjects\Models\People\ListConfig;
use ExpertsCrm\Enums\SortOptions;
use ExpertsCrm\Validators\BaseValidator;
use Ramsey\Uuid\Uuid;


defined( 'ABSPATH' ) || exit;

class ListValidator extends BaseValidator {
    function __construct() {
        $this->rules = [
            "listId" => fn($data) => !$data || Uuid::isValid($data),
            "sort" => fn($data) => !$data || !!SortOptions::tryFrom($data),
            "pointer" => fn($data) => !!is_int($data),
            "query" => fn($data) => !$data || is_string($data),
            "expanded" => fn($data) => !$data || is_bool($data),
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

    function getConfigObject()
    {
        return new ListConfig(
          $this->data["pointer"],
          $this->data["query"],
          $this->data["listId"],
          $this->data["expanded"],
          $this->data["sort"] ?? ""
        );
    }
}