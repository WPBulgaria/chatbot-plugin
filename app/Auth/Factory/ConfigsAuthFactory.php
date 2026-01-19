<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ConfigsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ConfigsAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;
defined( 'ABSPATH' ) || exit;

class ConfigsAuthFactory {
    public static function create(ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_DEBUG && _WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new ConfigsAuthMock($configsModel);
        }
        return new ConfigsAuth($configsModel);
    }
}