<?php

namespace ExpertsCrm\Validators\People;

use ExpertsCrm\Validators\BaseValidator;

defined( 'ABSPATH' ) || exit;

class AddressListValidator extends BaseValidator {
    protected $addressValidator;
    function __construct() {
        $this->rules = [];
        $this->addressValidator = AddressValidator::make();
    }

    function isValid($data): bool
    {
        if (empty($data)) {
            return true;
        } else if (!is_array($data) || !is_array($data[0])) {
            $this->errors["addresses"] = "Invalid document";
        }

        $errors = [];
        foreach($data as $key => $action) {
            $isValid = $this->addressValidator->isValid($action);
            if (!$isValid) {
                $errors[$key] = $this->addressValidator->getErrors();
                $this->addressValidator->resetErrors();
            }
        }

        $this->errors = $errors;
        return empty($errors);
    }
}