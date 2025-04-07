<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/tasks/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\TaskAction::view',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/tasks/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\TaskAction::trash',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/tasks/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\TaskAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/tasks', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\TaskAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/tasks', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\TaskAction::list',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});