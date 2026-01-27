<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/plans/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\PlanAction::trash',
        'permission_callback' => function ($request) {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\PlansAuthFactory::class)->trash($request->get_param('id'));
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/plans/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'WPBulgaria\Chatbot\Actions\PlanAction::store',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\PlansAuthFactory::class)->store();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/plans', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\PlanAction::store',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\PlansAuthFactory::class)->store();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>.+)/plans', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\PlanAction::list',
        'permission_callback' => function () {
            return wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\PlansAuthFactory::class)->list();
        }
    ) );
});