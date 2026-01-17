<?php

namespace WPBulgaria\Chatbot\Models;

defined( 'ABSPATH' ) || exit;

class FileModel {

    const ALLOWED_TYPES = [
        'json' => 'application/json',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
        'xml' => 'application/xml'
    ];
    const MAX_FILE_SIZE = 10485760; // 10MB

    public static function list(int $per_page = 20, int $page = 1): array {
        $args = [
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_mime_type' => array_values(self::ALLOWED_TYPES),
        ];

        $query = new \WP_Query($args);
        $files = [];

        foreach ($query->posts as $attachment) {
            $files[] = self::formatAttachment($attachment);
        }

        return [
            'files' => $files,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages
        ];
    }

    public static function upload(array $file): array {
        $allowed_types_values = array_values(self::ALLOWED_TYPES);

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("File upload error", 400);
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \Exception("File size exceeds limit", 400);
        }

        $file_type = wp_check_filetype($file['name'], self::ALLOWED_TYPES);
        if (!in_array($file['type'], $allowed_types_values) || empty($file_type['ext'])) {
            throw new \Exception("File type not allowed", 400);
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        //The file type is already checked on line 56, so we don't need to test it again
        $upload_overrides = ['test_form' => false, 'test_type' => false];
        $uploaded = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded['error'])) {
            throw new \Exception($uploaded['error'], 400);
        }

        $attachment_data = [
            'post_mime_type' => $file['type'],
            'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'    => get_current_user_id(),
        ];

        $attachment_id = wp_insert_attachment($attachment_data, $uploaded['file']);

        if (is_wp_error($attachment_id)) {
            throw new \Exception("Failed to create attachment", 500);
        }

        $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_metadata);

        $attachment = get_post($attachment_id);

        return self::formatAttachment($attachment, $file['name']);
    }

    public static function remove(int $id): bool {
        $attachment = get_post($id);

        if (!$attachment || $attachment->post_type !== 'attachment') {
            throw new \Exception("File not found", 404);
        }

        $deleted = wp_delete_attachment($id, true);

        if (!$deleted) {
            throw new \Exception("Failed to delete file", 500);
        }

        return true;
    }

    public static function get(int $id): ?array {
        $attachment = get_post($id);

        if (!$attachment || $attachment->post_type !== 'attachment') {
            return null;
        }

        return self::formatAttachment($attachment);
    }

    private static function formatAttachment(\WP_Post $attachment, ?string $originalName = null): array {
        $file_path = get_attached_file($attachment->ID);
        
        return [
            'id'         => $attachment->ID,
            'url'        => wp_get_attachment_url($attachment->ID),
            'type'       => $attachment->post_mime_type,
            'name'       => $originalName ?? $attachment->post_title,
            'size'       => file_exists($file_path) ? filesize($file_path) : 0,
            'createdAt'  => $attachment->post_date_gmt,
            'modifiedAt' => $attachment->post_modified_gmt,
            'uploader'   => get_the_author_meta('display_name', $attachment->post_author),
            'uploaderId' => $attachment->post_author,
            'inUse'      => get_post_meta($attachment->ID, WPB_CHATBOT_FILE_IN_USE_FIELD, true) === '1',
        ];
    }
}
