<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class ChatbotAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(): bool {
        return true;
    }

    public function get(int|string $id): bool {
        return true;
    }

    public function store(): bool {
        return true;
    }

    public function update(int|string $id): bool {
        return true;
    }

    public function updateConfig(int|string $id): bool {
        return true;
    }

    public function trash(int|string $id): bool {
        return true;
    }

    public function remove(int|string $id): bool {
        return true;
    }

    public function restore(int|string $id): bool {
        return true;
    }
}
