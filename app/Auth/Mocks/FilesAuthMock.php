<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class FilesAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(int|string $userId, ...$args): bool {
        return true;
    }

    public function store(int|string $userId, ...$args): bool {
        return true;
    }

    public function upload(int|string $userId, ...$args): bool {
        return true;
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function use(int|string $userId, int|string $id): bool {
        return true;
    }
}
