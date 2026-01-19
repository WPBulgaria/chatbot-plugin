<?php

namespace WPBulgaria\Chatbot\Auth;

use WPBulgaria\Chatbot\Contracts\AuthInterface;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

abstract class BaseAuth implements AuthInterface {

    protected int $userId;
    protected ?\WP_User $wpUser;

    public function __construct(int $userId) {
        $this->userId = $userId;
        $this->wpUser = \get_user_by('id', $userId) ?: null;
    }

    public static function getInstance(int $userId): static {
        return new static($userId);
    }

    public function isAdminsOnly(): bool {
        $configs = ConfigsModel::view();
        return !empty($configs["adminsOnly"]);
    }

    /**
     * Default implementation - override in child classes
     */
    public function list(): bool {
        return current_user_can('manage_options');
    }

    /**
     * Default implementation - override in child classes
     */
    public function store(): bool {
        return current_user_can('manage_options');
    }

    /**
     * Default implementation - override in child classes
     */
    public function trash(string|int $id): bool {
        return current_user_can('manage_options');
    }

    /**
     * Default implementation - override in child classes
     */
    public function remove(string|int $id): bool {
        return current_user_can('manage_options');
    }
}
