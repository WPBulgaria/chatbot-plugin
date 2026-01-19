<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Auth\BaseAuth;

defined('ABSPATH') || exit;

class PlansAuthMock extends BaseAuth {

    public function __construct(int $userId) {
        parent::__construct($userId);
    }

    public function view(): bool {
        return true;
    }

    public function list(): bool {
        return true;
    }

    public function store(): bool {
        return true;
    }

    public function trash(int|string $id): bool {
        return true;
    }

    public function remove(int|string $id): bool {
        return true;
    }

    public function bulkTrash(array $ids): bool {
        return true;
    }

    public function bulkRemove(array $ids): bool {
        return true;
    }
}
