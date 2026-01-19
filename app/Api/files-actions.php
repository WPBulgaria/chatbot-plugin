<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::remove',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->remove($request->get_param('id'));
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::upload',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->upload();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::list',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->list();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/files/(?P<id>.+)/use', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::use',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->use($request->get_param('id'));
        }
    ) );
});