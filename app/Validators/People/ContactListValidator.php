<?php

namespace ExpertsCrm\Validators\People;

use ExpertsCrm\Validators\BaseValidator;

defined( 'ABSPATH' ) || exit;

class ContactListValidator extends BaseValidator {
    protected $contactValidator;
    function __construct() {
        $this->rules = [];
        $this->contactValidator = ContactValidator::make();
    }

    function isValid($data): bool
    {
        if (empty($data)) {
            return true;
        } else if (!is_array($data) || !is_array($data[0])) {
            $this->errors["contacts"] = "Invalid document";
        }

        $errors = [];
        foreach($data as $key => $action) {
            $isValid = $this->contactValidator->isValid($action);
            if (!$isValid) {
                $errors[$key] = $this->contactValidator->getErrors();
                $this->contactValidator->resetErrors();
            }
        }

        $this->errors = $errors;
        return empty($errors);
    }
}