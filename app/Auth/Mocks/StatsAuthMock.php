<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

class StatsAuthMock extends BaseAuthMock {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    /**
     * Mock: Allow viewing stats
     */
    public function view(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow viewing period stats
     */
    public function getPeriodStats(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow viewing global stats
     */
    public function getGlobalStats(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow viewing comparative stats
     */
    public function getComparativeStats(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow viewing activity chart
     */
    public function getActivityChart(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow viewing top users
     */
    public function getTopUsers(int|string $userId, ...$args): bool {
        return true;
    }

    /**
     * Mock: Allow clearing cache
     */
    public function clearCache(int|string $userId, ...$args): bool {
        return true;
    }
}
