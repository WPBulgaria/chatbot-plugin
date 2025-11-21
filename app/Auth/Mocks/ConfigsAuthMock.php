<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Auth\BaseAuth;

defined( 'ABSPATH' ) || exit;


class ConfigsAuthMock extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function view(): bool {
        return true;
    }
    
    public function list() {
        return true;   
    }
    public function store(): bool {
        return true;
    }

    public function trash(string $id): bool {
        return true;
    }
    public function remove(string $id): bool {
        return true;
    }
    public function bulkTrash(array $ids): bool {
        return true;
    }
    public function bulkRemove(array $ids): bool {
        return true;
    }
}