<?php

namespace WPBulgaria\Chatbot\Auth;

defined( 'ABSPATH' ) || exit;


class FilesAuth extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function list() {
        return user_can($this->userId, 'manage_options');
    }

    public function upload(): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function remove(string $id): bool {
        return user_can($this->userId, 'manage_options');
    }
    public function use(string $id): bool {
        return user_can($this->userId, 'manage_options');
    }
}
