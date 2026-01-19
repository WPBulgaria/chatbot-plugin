<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\PlansAuth;
use WPBulgaria\Chatbot\Auth\Mocks\PlansAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel; 
defined( 'ABSPATH' ) || exit;

class PlansAuthFactory {
    public static function create(int $userId, ConfigsModel $configsModel) {
        if (_WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new PlansAuthMock($userId, $configsModel);  
        }
        return new PlansAuth($userId, $configsModel);
    }
}