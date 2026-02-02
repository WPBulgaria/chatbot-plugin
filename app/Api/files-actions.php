<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/files/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::remove',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->remove(get_current_user_id(), $request->get_param('id'));
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/files', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::upload',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->upload(get_current_user_id());
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/files', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::list',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->list(get_current_user_id());
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/files/(?P<id>.+)/use', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\FileAction::use',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory::class)->use(get_current_user_id(), $request->get_param('id'));
        }
    ) );
});