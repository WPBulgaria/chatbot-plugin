<?php

namespace WPBulgaria\Chatbot\Models;

defined('ABSPATH') || exit;

class ChatbotModel extends BaseModel {

    const POST_TYPE = 'wpb_chatbot';
    const META_CONFIG = '_wpb_chatbot_config';


    protected PostModel $postModel;
    protected ConfigsModel $configsModel;

    public function __construct(PostModel $postModel, ConfigsModel $configsModel) {
        parent::__construct();
        $this->postModel = $postModel;
        $this->configsModel = $configsModel;
    }

    /**
     * List chatbots with pagination
     */
    public function list(int $perPage = 20, int $page = 1): array {
        $args = [
            'post_type'      => self::POST_TYPE,
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $query = new \WP_Query($args);
        $chatbots = [];

        foreach ($query->posts as $post) {
            $chatbots[] = $this->formatChatbot($post);
        }

        return [
            'chatbots' => $chatbots,
            'total'    => $query->found_posts,
            'pages'    => $query->max_num_pages
        ];
    }

    /**
     * Get a single chatbot by ID
     */
    public function get(int $id): ?array {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE || $post->post_status === 'trash') {
            return null;
        }

        return $this->formatChatbot($post);
    }

    /**
     * Get chatbot config
     */
    public function getConfig(int|string $id): array {
        return $this->configsModel->view($id, true);
    }

    /**
     * Create a new chatbot
     */
    public function create(array $data): int {
        $postId = $this->postModel->insert([
            'post_type'    => self::POST_TYPE,
            'post_title'   => sanitize_text_field($data['title'] ?? 'New Chatbot'),
            'post_content' => wp_kses_post($data['description'] ?? ''),
            'post_status'  => 'publish',
        ], true);

        if (is_wp_error($postId)) {
            throw new \Exception("Failed to create chatbot: " . $postId->get_error_message(), 500);
        }

        $this->configsModel->store($postId, $data['config'] ?? []);

        return $postId;
    }

    /**
     * Update chatbot
     */
    public function update(int $id, array $data): bool {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chatbot not found", 404);
        }

        $updateData = ['ID' => $id];

        if (isset($data['title'])) {
            $updateData['post_title'] = sanitize_text_field($data['title']);
        }

        if (isset($data['description'])) {
            $updateData['post_content'] = wp_kses_post($data['description']);
        }

        $result = $this->postModel->update($updateData, true);

        if (is_wp_error($result)) {
            throw new \Exception("Failed to update chatbot", 500);
        }

        if (isset($data['config'])) {
            $this->configsModel->store($id, $data['config']);
        }

        return true;
    }

    /**
     * Trash a chatbot (soft delete)
     */
    public function trash(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chatbot not found", 404);
        }

        $result = $this->postModel->trash($id);

        if (!$result) {
            throw new \Exception("Failed to trash chatbot", 500);
        }

        return true;
    }

    /**
     * Permanently delete a chatbot
     */
    public function remove(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chatbot not found", 404);
        }

        $result = $this->postModel->delete($id);

        if (!$result) {
            throw new \Exception("Failed to delete chatbot", 500);
        }

        return true;
    }

    /**
     * Restore a trashed chatbot
     */
    public function restore(int $id): bool {
        $post = $this->postModel->get($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            throw new \Exception("Chatbot not found", 404);
        }

        $result = $this->postModel->untrash($id);

        if (!$result) {
            throw new \Exception("Failed to restore chatbot", 500);
        }

        return true;
    }

    /**
     * Format chatbot post for API response
     */
    private function formatChatbot(\WP_Post $post): array {
        $config = $this->getConfig($post->ID);

        return [
            'id'          => $post->ID,
            'title'       => $post->post_title ?: "Untitled Chatbot",
            'description' => $post->post_content,
            'createdAt'   => date('Y-m-d H:i:s', strtotime($post->post_date_gmt)),
            'modifiedAt'  => date('Y-m-d H:i:s', strtotime($post->post_modified_gmt)),
            'config'      => $config,
            'status'      => $post->post_status,
        ];
    }
}
