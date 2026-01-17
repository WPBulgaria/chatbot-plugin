<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

defined( 'ABSPATH' ) || exit;

use WPBulgaria\Chatbot\Auth\BaseAuth;

class ChatsAuthMock extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function list(): bool {
        return true;
    }

    public function get($id): bool {
        return true;
    }

    public function chat($id = null): bool {
        return true;
    }

    public function updateTitle($id): bool {
        return true;
    }

    public function trash($id): bool {
        return true;
    }

    public function remove($id): bool {
        return true;
    }

    public function restore($id): bool {
        return true;
    }
}
