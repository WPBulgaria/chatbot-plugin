<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class ChatbotAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(int|string $userId, ...$args): bool {
        return true;
    }

    public function get(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function store(int|string $userId, ...$args): bool {
        return true;
    }

    public function update(int|string $userId, int|string $id): bool {
        return true;
    }

    public function updateConfig(int|string $id): bool {
        return true;
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function restore(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }
}
