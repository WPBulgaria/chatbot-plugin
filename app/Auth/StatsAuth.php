<?php

namespace WPBulgaria\Chatbot\Auth;

use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\DataObjects\Auth\AuthError;

defined('ABSPATH') || exit;

class StatsAuth extends BaseAuth {

    public function __construct(ConfigsModel $configsModel) {
        parent::__construct($configsModel);
    }

    /**
     * Check if user can view stats
     */
    public function view(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view statistics'));
        });
    }

    /**
     * Check if user can view period stats
     */
    public function getPeriodStats(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view period statistics'));
        });
    }

    /**
     * Check if user can view global stats
     */
    public function getGlobalStats(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view global statistics'));
        });
    }

    /**
     * Check if user can view comparative stats
     */
    public function getComparativeStats(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view comparative statistics'));
        });
    }

    /**
     * Check if user can view activity chart
     */
    public function getActivityChart(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view activity charts'));
        });
    }

    /**
     * Check if user can view top users
     */
    public function getTopUsers(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to view top users'));
        });
    }

    /**
     * Check if user can clear stats cache
     */
    public function clearCache(int|string $userId, ...$args): bool {
        return $this->check($this->currentUserCan('manage_options'), function() {
            $this->setError(new AuthError('manage_options', 'You are not allowed to clear statistics cache'));
        });
    }
}
