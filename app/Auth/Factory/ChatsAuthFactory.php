<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ChatsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ChatsAuthMock;

defined( 'ABSPATH' ) || exit;

class ChatsAuthFactory {
    public static function create(int $userId) {
        if (_WPB_CHATBOT_UNLOCK_API === "unlock it all now") {
            return new ChatsAuthMock($userId);
        }
        return new ChatsAuth($userId);
    }
}
