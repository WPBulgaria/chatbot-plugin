<?php

use WPBulgaria\Chatbot\Models\ChatModel;

defined( 'ABSPATH' ) || exit;

add_action('init', function() {
    register_post_type(ChatModel::POST_TYPE, [
        'labels' => [
            'name'               => __('Chats', 'wpbulgaria-chatbot'),
            'singular_name'      => __('Chat', 'wpbulgaria-chatbot'),
            'add_new'            => __('Add New', 'wpbulgaria-chatbot'),
            'add_new_item'       => __('Add New Chat', 'wpbulgaria-chatbot'),
            'edit_item'          => __('Edit Chat', 'wpbulgaria-chatbot'),
            'new_item'           => __('New Chat', 'wpbulgaria-chatbot'),
            'view_item'          => __('View Chat', 'wpbulgaria-chatbot'),
            'search_items'       => __('Search Chats', 'wpbulgaria-chatbot'),
            'not_found'          => __('No chats found', 'wpbulgaria-chatbot'),
            'not_found_in_trash' => __('No chats found in Trash', 'wpbulgaria-chatbot'),
            'all_items'          => __('All Chats', 'wpbulgaria-chatbot'),
            'menu_name'          => __('Chats', 'wpbulgaria-chatbot'),
        ],
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => false,
        'show_in_menu'        => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'supports'            => ['title', 'author'],
        'map_meta_cap'        => true,
    ]);
});

add_action('init', function() {
    register_post_meta(ChatModel::POST_TYPE, ChatModel::META_MESSAGES, [
        'type'              => 'string',
        'description'       => 'Chat messages history as JSON',
        'single'            => true,
        'show_in_rest'      => false,
        'sanitize_callback' => function($value) {
            if (is_array($value)) {
                return wp_json_encode($value);
            }
            return $value;
        },
    ]);
});
