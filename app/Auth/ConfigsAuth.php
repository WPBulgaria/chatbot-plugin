<?php

namespace WPBulgaria\Chatbot\Auth;

defined( 'ABSPATH' ) || exit;


class ConfigsAuth extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function view(): bool {
        return user_can($this->userId, 'manage_options');
    }
    
    public function list() {
        return user_can($this->userId, 'manage_options');   
    }
    public function store(): bool {
        return user_can($this->userId, 'manage_options');
    }

    public function trash(string $id): bool {
        return user_can($this->userId, 'manage_options');
    }
    public function remove(string $id): bool {
        return user_can($this->userId, 'manage_options');
    }
    public function bulkTrash(array $ids): bool {
        return user_can($this->userId, 'manage_options');
    }
    public function bulkRemove(array $ids): bool {
        return user_can($this->userId, 'manage_options');
    }
}