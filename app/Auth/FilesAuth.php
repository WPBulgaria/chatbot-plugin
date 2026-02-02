<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class FilesAuth extends BaseAuth {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function list(int|string $userId, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }

    public function store(int|string $userId, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }

    public function upload(int|string $userId, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }

    public function use(int|string $userId, int|string $id, ...$args): bool {
        return $this->userCan($userId, 'manage_options');
    }
}
