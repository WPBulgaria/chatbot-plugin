<?php

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Auth\Factory\ChatbotAuthFactory;
// GET /chatbots - List all chatbots
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots', array(
        'methods'             => 'GET',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::list',
        'permission_callback' => function () {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->list();
        }
    ));
});

// GET /chatbots/{id} - Get specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<id>\d+)', array(
        'methods'             => 'GET',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::get',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->get($request->get_param('id'));
        }
    ));
});

// POST /chatbots - Create new chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots', array(
        'methods'             => 'POST',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::create',
        'permission_callback' => function () {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->store();
        }
    ));
});

// PUT /chatbots/{id} - Update chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<id>\d+)', array(
        'methods'             => 'PUT',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::update',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->update($request->get_param('id'));
        }
    ));
});

// DELETE /chatbots/{id} - Trash chatbot (soft delete)
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<id>\d+)', array(
        'methods'             => 'DELETE',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::trash',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->trash($request->get_param('id'));
        }
    ));
});

// DELETE /chatbots/{id}/force - Permanently delete chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<id>\d+)/force', array(
        'methods'             => 'DELETE',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::remove',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->remove($request->get_param('id'));
        }
    ));
});

// POST /chatbots/{id}/restore - Restore trashed chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<id>\d+)/restore', array(
        'methods'             => 'POST',
        'callback'            => 'WPBulgaria\Chatbot\Actions\ChatbotAction::restore',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(ChatbotAuthFactory::class)->restore($request->get_param('id'));
        }
    ));
});
