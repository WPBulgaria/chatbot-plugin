<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ChatsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ChatsAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined( 'ABSPATH' ) || exit;

class ChatsAuthFactory {
    public static function create(int $userId, ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new ChatsAuthMock($userId, $configsModel);
        }
        return new ChatsAuth($userId, $configsModel);
    }
}
