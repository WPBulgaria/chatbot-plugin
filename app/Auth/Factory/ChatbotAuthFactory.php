<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ChatbotAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ChatbotAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class ChatbotAuthFactory {
    public static function create(ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_DEBUG && _WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new ChatbotAuthMock($configsModel);
        }
        return new ChatbotAuth($configsModel);
    }
}
