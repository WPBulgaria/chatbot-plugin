<?php

use WPBulgaria\Chatbot\Models\ChatbotModel;
use WPBulgaria\Chatbot\Validators\Configs\ConfigsValidator;

defined('ABSPATH') || exit;

add_action('init', function() {
    register_post_type(ChatbotModel::POST_TYPE, [
        'labels' => [
            'name'               => __('Chatbots', 'wpbulgaria-chatbot'),
            'singular_name'      => __('Chatbot', 'wpbulgaria-chatbot'),
            'add_new'            => __('Add New', 'wpbulgaria-chatbot'),
            'add_new_item'       => __('Add New Chatbot', 'wpbulgaria-chatbot'),
            'edit_item'          => __('Edit Chatbot', 'wpbulgaria-chatbot'),
            'new_item'           => __('New Chatbot', 'wpbulgaria-chatbot'),
            'view_item'          => __('View Chatbot', 'wpbulgaria-chatbot'),
            'search_items'       => __('Search Chatbots', 'wpbulgaria-chatbot'),
            'not_found'          => __('No chatbots found', 'wpbulgaria-chatbot'),
            'not_found_in_trash' => __('No chatbots found in Trash', 'wpbulgaria-chatbot'),
            'all_items'          => __('All Chatbots', 'wpbulgaria-chatbot'),
            'menu_name'          => __('Chatbots', 'wpbulgaria-chatbot'),
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
        'supports'            => ['title', 'editor'],
        'map_meta_cap'        => true,
    ]);
});

add_action('init', function() {
    register_post_meta(ChatbotModel::POST_TYPE, ChatbotModel::META_CONFIG, [
        'type'              => 'string',
        'description'       => 'Chatbot configuration as JSON',
        'single'            => true,
        'show_in_rest'      => false,
        'sanitize_callback' => function($value) {
            $validator = ConfigsValidator::make();

            if ($validator->isValid($value)) {
                return $validator->getCleanData($value);
            }

           return [];
        },
    ]);
});
