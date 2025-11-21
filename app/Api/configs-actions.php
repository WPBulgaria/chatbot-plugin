<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/configs', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ConfigsAction::store',
        'permission_callback' => function () {
            return  \WPBulgaria\Chatbot\Auth\Factory\ConfigsAuthFactory::create(get_current_user_id())->store();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/configs', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ConfigsAction::view',
        'permission_callback' => function () {
            return  \WPBulgaria\Chatbot\Auth\Factory\ConfigsAuthFactory::create(get_current_user_id())->view();
        }   
    ) );
});