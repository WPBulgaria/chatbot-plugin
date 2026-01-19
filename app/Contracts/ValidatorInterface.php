<?php

namespace WPBulgaria\Chatbot\Contracts;

defined('ABSPATH') || exit;

/**
 * Interface for validator classes
 */
interface ValidatorInterface {

    /**
     * Create a new validator instance
     */
    public static function make(): self;

    /**
     * Validate a single field
     */
    public function isValidField(string $field, mixed $data): bool;

    /**
     * Validate all data against rules
     */
    public function isValid(array $data): bool;

    /**
     * Get validated and cleaned data
     */
    public function getCleanData(array $data): array;

    /**
     * Get validation errors
     */
    public function getErrors(): array;

    /**
     * Reset validation errors
     */
    public function resetErrors(): void;
}
