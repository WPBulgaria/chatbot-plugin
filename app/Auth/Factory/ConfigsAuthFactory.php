<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ConfigsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ConfigsAuthMock;

defined( 'ABSPATH' ) || exit;

class ConfigsAuthFactory {
    public static function create(int $userId) {
        if (_WPB_CHATBOT_UNLOCK_API) {
            return new ConfigsAuthMock($userId);
        }
        return new ConfigsAuth($userId);
    }
}