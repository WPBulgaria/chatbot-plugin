<?php

namespace WPBulgaria\Chatbot\Auth\Factory;

use WPBulgaria\Chatbot\Auth\PlansAuth;
use WPBulgaria\Chatbot\Auth\Mocks\PlansAuthMock;

defined( 'ABSPATH' ) || exit;

class PlansAuthFactory {
    public static function create(int $userId) {
        if (_WPB_CHATBOT_UNLOCK_API === "unlock it all now") {
            return new PlansAuthMock($userId);
        }
        return new PlansAuth($userId);
    }
}