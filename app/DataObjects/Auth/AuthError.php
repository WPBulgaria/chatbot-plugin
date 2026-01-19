<?php

namespace WPBulgaria\Chatbot\DataObjects\Auth;

defined('ABSPATH') || exit;

class AuthError {
    protected string $code;
    protected string $message;
    protected array $data;

    public function __construct(string $code, string $message, array $data = []) {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getData(): array {
        return $this->data;
    }
}