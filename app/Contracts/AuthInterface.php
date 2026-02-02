<?php

namespace WPBulgaria\Chatbot\Contracts;

defined('ABSPATH') || exit;

/**
 * Interface for authorization classes
 */
interface AuthInterface {
    
    /**
     * Check if user can list resources
     */
    public function list(int|string $userId, ...$args): bool;

    /**
     * Check if user can store/create resources
     */
    public function store(int|string $userId, ...$args): bool;

    /**
     * Check if user can trash a resource
     */
    public function trash(int|string $userId, int|string $id, ...$args): bool;

    /**
     * Check if user can permanently remove a resource
     */
    public function remove(int|string $userId, int|string $id, ...$args): bool;

    /**
     * Check if current user can perform a capability
     */
    public function currentUserCan(string $capability, ...$args): bool;

    public function currentUserId(): int;

    public function currentUser(): \WP_User;

    public function userCan(int $userId, string $capability, ...$args): bool;
}
