<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class ChatbotAuth extends BaseAuth {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(int|string $userId, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function get(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function store(int|string $userId, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function update(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function updateConfig(int|string $userId, int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return $this->currentUserCan('manage_options');
    }

    public function restore(int|string $userId = 0, int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }
}
