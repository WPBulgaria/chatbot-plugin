<?php

namespace WPBulgaria\Chatbot\Auth;

use WPBulgaria\Chatbot\Contracts\AuthInterface;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

abstract class BaseAuth implements AuthInterface {
    protected ConfigsModel $configsModel;

    public function __construct(ConfigsModel $configsModel) {
        $this->configsModel = $configsModel;
    }

    public static function getInstance(ConfigsModel $configsModel): static {
        return new static($configsModel);
    }

    public function isAdminsOnly(): bool {
        $configs = $this->configsModel->view();
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

    public function currentUserCan(string $capability, ...$args): bool {
        return current_user_can($capability, ...$args);
    }

    public function currentUserId(): int {
        return get_current_user_id();
    }

    public function currentUser(): \WP_User {
        return wp_get_current_user();
    }

    public function userCan(int $userId, string $capability, ...$args): bool {
        return user_can($userId, $capability, ...$args);
    }
}
