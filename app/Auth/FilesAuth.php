<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

class FilesAuth extends BaseAuth {

    public function __construct(int $userId) {
        parent::__construct($userId);
    }

    public function list(): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function store(): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function upload(): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function trash(int|string $id): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function remove(int|string $id): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function use(int|string $id): bool {
        return user_can($this->userId, 'manage_options');
    }
}
