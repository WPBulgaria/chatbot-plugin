<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::remove',
        'permission_callback' => function ($request) {
            return  \WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::create(get_current_user_id())->remove($request->get_param('id'));
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::upload',
        'permission_callback' => function () {
            return  \WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::create(get_current_user_id())->upload();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::list',
        'permission_callback' => function () {
            return  \WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::create(get_current_user_id())->list();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files/(?P<id>.+)/use', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::use',
        'permission_callback' => function ($request) {
            return  \WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::create(get_current_user_id())->use($request->get_param('id'));
        }
    ) );
});