<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/touches/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\TouchAction::view',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/touches/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\TouchAction::trash',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/touches/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\TouchAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/touches', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\TouchAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/touches', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\TouchAction::list',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});