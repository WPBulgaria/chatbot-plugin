<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/notes/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\NoteAction::view',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/notes/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\NoteAction::trash',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/notes/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\NoteAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/notes', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\NoteAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/notes', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\NoteAction::list',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});