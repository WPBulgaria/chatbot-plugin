<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\FilesAuth;
use WPBulgaria\Chatbot\Auth\Mocks\FilesAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;
defined( 'ABSPATH' ) || exit;

class FilesAuthFactory {
    public static function create(int $userId, ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new FilesAuthMock($userId, $configsModel);
        }
        return new FilesAuth($userId, $configsModel);
    }
}
