<?php

namespace ExpertsCrm\Validators\Action;

use ExpertsCrm\Validators\BaseValidator;

defined( 'ABSPATH' ) || exit;

class ActionListValidator extends BaseValidator {
    protected $actionValidator;
    function __construct() {
        $this->rules = [];
        $this->actionValidator = ActionValidator::make();
    }

    function isValid($data): bool
    {
        if (empty($data)) {
            return true;
        } else if (!is_array($data) || !is_array($data[0])) {
            $this->errors["actions"] = "Invalid document";
        }

        $errors = [];
        foreach($data as $key => $action) {
            $isValid = $this->actionValidator->isValid($action);
            if (!$isValid) {
                $errors[$key] = $this->actionValidator->getErrors();
                $this->actionValidator->resetErrors();
            }
        }

        $this->errors = $errors;
        return empty($errors);
    }
}