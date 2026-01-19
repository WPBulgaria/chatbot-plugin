<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;

class PlansAuth extends BaseAuth {  

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    public function view(): bool {
        return true;
    }

    public function list(): bool {
        return $this->currentUserCan('manage_options');
    }

    public function store(): bool {
        return $this->currentUserCan('manage_options');
    }

    public function trash(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function remove(int|string $id): bool {
        return $this->currentUserCan('manage_options');
    }

    public function bulkTrash(array $ids): bool {
        return $this->currentUserCan('manage_options');
    }

    public function bulkRemove(array $ids): bool {
        return $this->currentUserCan('manage_options');
    }
}
