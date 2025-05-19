<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/people/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\PeopleAction::view',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/people/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\PeopleAction::trash',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/people/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\PeopleAction::store',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/people', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\PeopleAction::store',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/people', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\PeopleAction::list',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ) );
});