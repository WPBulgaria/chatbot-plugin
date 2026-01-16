<?php

namespace WPBulgaria\Chatbot\Actions;

defined( 'ABSPATH' ) || exit;

class FileAction {

    private static $allowed_types = ['json' => 'application/json', 'pdf' => 'application/pdf', 'txt' => 'text/plain', 'csv' => 'text/csv', 'tsv' => 'text/tab-separated-values', 'xml' => 'application/xml'];
    private static $max_file_size = 10485760; // 10MB

    static function upload(\WP_REST_Request $request) {
        $files = $request->get_file_params();
        $allowed_types_values = array_values(self::$allowed_types);

        if (empty($files['file'])) {
            return new \WP_REST_Response(["success" => false, "message" => "No file provided"], 400);
        }

        $file = $files['file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new \WP_REST_Response(["success" => false, "message" => "File upload error"], 400);
        }

        if ($file['size'] > self::$max_file_size) {
            return new \WP_REST_Response(["success" => false, "message" => "File size exceeds limit"], 400);
        }

        $file_type = wp_check_filetype($file['name'], self::$allowed_types);
        if (!in_array($file['type'], $allowed_types_values) || empty($file_type['ext'])) {
            return new \WP_REST_Response(["success" => false, "message" => "File type not allowed"], 400);
        }

        
            

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $upload_overrides = ['test_form' => false, "test_type" => false];
        $uploaded = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded['error'])) {
            return new \WP_REST_Response(["success" => false, "message" => $uploaded['error']], 400);
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
            return new \WP_REST_Response(["success" => false, "message" => "Failed to create attachment"], 500);
        }

        $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_metadata);

        $attachment = get_post($attachment_id);

        return new \WP_REST_Response([
            "success" => true,
            "file" => [
                "id"   => $attachment_id,
                "url"  => $uploaded['url'],
                "path" => get_attached_file($attachment_id),
                "type" => $uploaded['type'],
                "name" => $file['name'],
                "size" => filesize(get_attached_file($attachment_id)),
                "createdAt" => $attachment->post_date_gmt,
                "modifiedAt" => $attachment->post_modified_gmt,
                "uploader" => get_the_author_meta('display_name', $attachment->post_author),
                "uploaderId" => $attachment->post_author,
                "inUse" => get_post_meta($attachment->ID, 'in_use', true),
            ]
        ], 200);
    }

    static function remove(\WP_REST_Request $request) {
        $data = $request->get_params();
        $id = isset($data['id']) ? absint($data['id']) : 0;

        if (empty($id)) {
            return new \WP_REST_Response(["success" => false, "message" => "Invalid file ID"], 400);
        }

        $attachment = get_post($id);

        if (!$attachment || $attachment->post_type !== 'attachment') {
            return new \WP_REST_Response(["success" => false, "message" => "File not found"], 404);
        }

        $deleted = wp_delete_attachment($id, true);

        if (!$deleted) {
            return new \WP_REST_Response(["success" => false, "message" => "Failed to delete file"], 500);
        }

        return new \WP_REST_Response(["success" => true], 200);
    }

    static function list(\WP_REST_Request $request) {
        $params = $request->get_params();
        $per_page = isset($params['per_page']) ? absint($params['per_page']) : 20;
        $page = isset($params['page']) ? absint($params['page']) : 1;

        $args = [
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_mime_type' => array_values(self::$allowed_types),
        ];

        $query = new \WP_Query($args);
        $files = [];

        foreach ($query->posts as $attachment) {
            $files[] = [
                'id'   => $attachment->ID,
                'url'  => wp_get_attachment_url($attachment->ID),
                'name' => $attachment->post_title,
                'type' => $attachment->post_mime_type,
                'size' => filesize(get_attached_file($attachment->ID)),
                'path' => get_attached_file($attachment->ID),
                'createdAt' => $attachment->post_date_gmt,
                'modifiedAt' => $attachment->post_modified_gmt,
                'uploader' => get_the_author_meta('display_name', $attachment->post_author),
                'uploaderId' => $attachment->post_author,
                'inUse' => get_post_meta($attachment->ID, 'in_use', true),
            ];
        }

        return new \WP_REST_Response([
            "success" => true,
            "files"   => $files,
            "total"   => $query->found_posts,
            "pages"   => $query->max_num_pages
        ], 200);
    }
}
