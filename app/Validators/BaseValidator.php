<?php

namespace ExpertsCrm\Validators;

defined( 'ABSPATH' ) || exit;

class BaseValidator {
    protected $errors = [];
    protected $rules = [];
    protected $messages = [];
    protected $data = [];

    static function make()
    {
        return new static();
    }

    public function isValidField($field, $data) {
        $fields = array_keys($this->rules);
        $errors = [];
        if (!in_array($field, $fields)) {
            $errors[$field] = "Unknown field '{$field}'";

        } else if ($this->rules[$field] instanceof BaseValidator) {

                if (!$this->rules[$field]->isValid($data)) {
                    $errors = array_merge($this->rules[$field]->getErrors(), $errors);
                }
        } else if (!$this->rules[$field]($data)) {
            $errors[$field] = $this->messages[$field] ?? "Invalid field '{$field}'";
        }

        $this->errors = $errors;
        return empty($errors);
    }
    public function isValid($data): bool
    {
        $errors = array();
        $fields = array_keys($this->rules);
        foreach ($this->rules as $field => $rule) {
            if (!in_array($field, $fields)) {
                $errors[$field] = "Unknown field '{$field}'";
            } else if (isset($data[$field]) && $this->rules[$field] instanceof BaseValidator) {
                if (!$this->rules[$field]->isValid($data[$field])) {
                    $errors = array_merge($this->rules[$field]->getErrors(), $errors);
                }
            } else if (isset($data[$field]) && !$this->rules[$field]($data[$field])) {
                $errors[$field] = $this->messages[$field] ?? "Invalid field '{$field}'";
            }
        }

        $this->errors = $errors;
        $isValid = empty($errors);
        if ($isValid) {
            $this->data = $data;
        }

        return $isValid;
    }

    public function getCleanData(array $data)
    {
        $clean = [];
        foreach ($this->rules as $field => $rule) {
            if (isset($data[$field])) {
                $clean[$field] = $data[$field];
            }
        }

        return $clean;
    }

    public function getConfigObject() {
        return new \stdClass();
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function resetErrors(): void {
        $this->errors = [];
    }
}