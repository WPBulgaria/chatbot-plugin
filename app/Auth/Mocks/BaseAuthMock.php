<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Auth\BaseAuth;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class BaseAuthMock extends BaseAuth {

    public function __construct(int $userId, ConfigsModel $configsModel) {
        parent::__construct($userId, $configsModel);
    }


    public function isAdminsOnly(): bool {
        return false;
    }

    public function currentUserCan(string $capability, ...$args): bool {
        return true;
    }

    public function currentUserId(): int {
        return 0;
    }

    public function currentUser(): \WP_User {
        return new \WP_User(0);
    }

    public function userCan(int $userId, string $capability, ...$args): bool {
        return true;
    }

}