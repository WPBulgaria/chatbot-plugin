<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class PlansAuth extends BaseAuth {  

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function view(int|string $userId): bool {
        return true;
    }

    public function list(int|string $userId, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function store(int|string $userId, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function bulkTrash(int|string $userId, array $ids, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function bulkRemove(int|string $userId, array $ids, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }
}
