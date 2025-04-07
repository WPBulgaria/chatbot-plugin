<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/lists/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\ListAction::view',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/lists/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\ListAction::trash',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/lists/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\ListAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/lists', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\ListAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/lists', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\ListAction::list',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});