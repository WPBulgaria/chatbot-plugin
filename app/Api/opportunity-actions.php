<?php

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/opportunities/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\OpportunityAction::view',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
} );

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/opportunities/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\OpportunityAction::trash',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/opportunities/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\OpportunityAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/opportunities', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\OpportunityAction::store',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'experts-crm/v1', '/opportunities', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\OpportunityAction::list',
        'permission_callback' => function () {
            return current_user_can("manage_options");
        }
    ) );
});