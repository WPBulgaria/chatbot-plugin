<?php

defined( 'ABSPATH' ) || exit;

// GET /chats - List all chats
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::list',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->list((int) $request->get_param('user_id'));
        }
    ) );
});

// GET /chats/{id} - Get specific chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::get',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->get($request->get_param('id'));
        }
    ) );
});

// POST /chats - Create new chat with message
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::chat',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->chat();
        }
    ) );
});

// POST /chats/{id} - Continue existing chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::chat',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->chat($request->get_param('id'));
        }
    ) );
});

// PATCH /chats/{id} - Update chat title
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::updateTitle',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->updateTitle($request->get_param('id'));
        }
    ) );
});

// DELETE /chats/{id} - Trash chat (soft delete)
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::trash',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->trash($request->get_param('id'));
        }
    ) );
});

// DELETE /chats/{id}/force - Permanently delete chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)/force', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::remove',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->remove($request->get_param('id'));
        }
    ) );
});

// POST /chats/{id}/restore - Restore trashed chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)/restore', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::restore',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->restore($request->get_param('id'));
        }
    ) );
});


// POST /chats/stream - Create new chat and stream the response
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/stream', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::stream',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->stream();
        }
    ) );
});

// POST /chats/{id}/stream - Continue existing chat and stream the response
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)/stream', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::stream',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class)->stream($request->get_param('id'));
        }
    ) );
});
