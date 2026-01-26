<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class ChatbotAuth extends BaseAuth {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(): bool {
        return $this->currentUserCan('manage_options');
    }

    public function get(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function store(): bool {
        return $this->currentUserCan('manage_options');
    }

    public function update(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function updateConfig(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function trash(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function remove(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function restore(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }
}
