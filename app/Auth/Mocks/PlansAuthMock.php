<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class PlansAuthMock extends BaseAuthMock {

    public function __construct(int $userId, ConfigsModel $configsModel) {
        parent::__construct($userId, $configsModel);    
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
