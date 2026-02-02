<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class PlansAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function view(int|string $userId = 0): bool {
        return true;
    }

    public function list(int|string $userId, ...$args): bool {
        return true;
    }

    public function store(int|string $userId, ...$args): bool {
        return true;
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function bulkTrash(int|string $userId, array $ids, ...$args): bool {
        return true;
    }

    public function bulkRemove(int|string $userId, array $ids, ...$args): bool {
        return true;
    }
}
