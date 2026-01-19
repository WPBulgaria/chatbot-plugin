<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;


use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class ChatsAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(int $userId = 0): bool {
        return true;
    }

    public function get(int|string $id): bool {
        return true;
    }

    public function store(): bool {
        return true;
    }

    public function chat(int|string|null $id = null): bool {
        return true;
    }

    public function stream(int|string|null $id = null): bool {
        return true;
    }

    public function updateTitle(int|string $id): bool {
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
