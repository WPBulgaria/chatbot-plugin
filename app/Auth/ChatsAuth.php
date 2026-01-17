<?php

namespace WPBulgaria\Chatbot\Auth;

defined( 'ABSPATH' ) || exit;

class ChatsAuth extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function list(): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return user_can($this->userId, 'manage_options');
    }

    public function get($id): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function chat($id = null): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function updateTitle($id): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }

    public function trash($id): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('delete_others_posts') || current_user_can('delete_post', $id);
    }

    public function remove($id): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('delete_others_posts') || current_user_can('delete_post', $id);
    }

    public function restore($id): bool {
        if ($this->isAdminsOnly()) {
            return user_can($this->userId, 'manage_options');
        }
        return current_user_can('edit_others_posts') || current_user_can('edit_post', $id);
    }
}
