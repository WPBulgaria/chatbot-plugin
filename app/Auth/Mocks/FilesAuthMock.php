<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

defined( 'ABSPATH' ) || exit;

use WPBulgaria\Chatbot\Auth\BaseAuth;


class FilesAuthMock extends BaseAuth {
    public function __construct($userId) {
        parent::__construct($userId);
    }

    public function list() {
        return true;
    }

    public function upload(): bool {
        return true;
    }

    public function remove(string $id): bool {
        return true;
    }
}
