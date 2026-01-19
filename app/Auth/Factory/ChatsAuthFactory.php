<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\ChatsAuth;
use WPBulgaria\Chatbot\Auth\Mocks\ChatsAuthMock;
use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Services\PlanService;

defined( 'ABSPATH' ) || exit;

class ChatsAuthFactory {
    public static function create(ConfigsModel $configsModel, ?PlanService $planService = null) {
        if (_WPB_CHATBOT_DEBUG && _WPB_CHATBOT_UNLOCK_API === "!!!unlock it all now") {
            return new ChatsAuthMock($configsModel, $planService);
        }
        return new ChatsAuth($configsModel, $planService);
    }
}
