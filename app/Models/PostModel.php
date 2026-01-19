<?php

namespace WPBulgaria\Chatbot\Models;

defined('ABSPATH') || exit;

class PostModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function insert(array $post, bool $wpError = false): int | string | \WP_Error {
        return wp_insert_post($post, $wpError);
    }

    public function updateMeta(int|string $postId, string $metaKey, mixed $metaValue): bool {
        return update_post_meta($postId, $metaKey, $metaValue);
    }

    public function update(array $post, bool $wpError = false): bool | \WP_Error {
        return wp_update_post($post, $wpError);
    }

    public function trash(int|string $postId): bool {
        return wp_trash_post($postId);
    }

    public function untrash(int|string $postId): bool {
        return wp_untrash_post($postId);
    }

    public function delete(int|string $postId): bool {
        return wp_delete_post($postId, true);
    }

    public function get(int|string $postId): ?\WP_Post {
        return get_post($postId);
    }

    public function getMeta(int|string $postId, string $metaKey): mixed {
        return get_post_meta($postId, $metaKey, true);
    }

    public function getAuthorMeta(int|string $userId, string $metaKey): mixed {
        return get_the_author_meta($metaKey, $userId);
    }
}   