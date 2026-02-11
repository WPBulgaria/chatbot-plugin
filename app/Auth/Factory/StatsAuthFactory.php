<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\StatsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\StatsAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class StatsAuthFactory {
    public static function create(ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_DEBUG && _WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new StatsAuthMock($configsModel);
        }
        return new StatsAuth($configsModel);
    }
}
