<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Auth\BaseAuth;

defined('ABSPATH') || exit;

class FilesAuthMock extends BaseAuth {

    public function __construct(int $userId) {
        parent::__construct($userId);
    }

    public function list(): bool {
        return true;
    }

    public function store(): bool {
        return true;
    }

    public function upload(): bool {
        return true;
    }

    public function trash(int|string $id): bool {
        return true;
    }

    public function remove(int|string $id): bool {
        return true;
    }

    public function use(int|string $id): bool {
        return true;
    }
}
