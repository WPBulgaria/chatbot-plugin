<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

class ChatsAuth extends BaseAuth {

    public function __construct(int $userId) {
        parent::__construct($userId);
    }

    public function list($userId = 0): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }

        if (!empty($userId) && $userId > 0 && !current_user_can('edit_others_posts')) {
            return $userId === $this->userId;
        }

        return current_user_can('edit_others_posts');
    }

    public function get(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function store(): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_posts');
    }

    public function chat(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function stream(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function updateTitle(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function trash(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('delete_others_posts') || current_user_can('delete_post', $id);
    }

    public function remove(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('delete_others_posts') || current_user_can('delete_post', $id);
    }

    public function restore(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return current_user_can('manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }
}
