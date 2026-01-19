<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class ChatsAuth extends BaseAuth {

    public function __construct(int $userId, ConfigsModel $configsModel) {
        parent::__construct($userId, $configsModel);
    }

    public function list($userId = 0): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }

        if (!empty($userId) && $userId > 0 && !$this->currentUserCan('edit_others_posts')) {
            return $userId === $this->userId;
        }

        return $this->currentUserCan('edit_others_posts');
    }

    public function get(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }

    public function store(): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_posts');
    }

    public function chat(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }

    public function stream(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }

    public function updateTitle(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }

    public function trash(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('delete_others_posts') || $this->currentUserCan('delete_post', $id);
    }

    public function remove(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('delete_others_posts') || $this->currentUserCan('delete_post', $id);
    }

    public function restore(int|string $id): bool {
        if ($this->isAdminsOnly()) {    
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }
}
