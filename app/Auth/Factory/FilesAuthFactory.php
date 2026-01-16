<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\FilesAuth;
use WPBulgaria\Chatbot\Auth\Mocks\FilesAuthMock;

defined( 'ABSPATH' ) || exit;

class FilesAuthFactory {
    public static function create(int $userId) {
        if (_WPB_CHATBOT_UNLOCK_API) {
            return new FilesAuthMock($userId);
        }
        return new FilesAuth($userId);
    }
}
