<?php

namespace WPBulgaria\Chatbot\Auth;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined( 'ABSPATH' ) || exit;

class BaseAuth {

    protected $userId;
    protected $wpUser;

    public function __construct($userId) {
        $this->userId = $userId;
        $this->wpUser = \get_user_by('id', $userId);
    }
    
    public static function getInstance($userId) {
        return new static($userId);
    }

    public function isAdminsOnly(): bool {
        $configs = ConfigsModel::view();
        return !!$configs["adminsOnly"];
    }
}