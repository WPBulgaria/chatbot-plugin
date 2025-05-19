<?php

defined( 'ABSPATH' ) || exit;

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/campaigns/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\CampaignAction::view',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/campaigns/(?P<id>.+)', array(
        'methods' => 'DELETE',
        'callback' => 'ExpertsCrm\Actions\CampaignAction::trash',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/campaigns/(?P<id>.+)', array(
        'methods' => 'PUT',
        'callback' => 'ExpertsCrm\Actions\CampaignAction::store',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/campaigns', array(
        'methods' => 'POST',
        'callback' => 'ExpertsCrm\Actions\CampaignAction::store',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/campaigns', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\CampaignAction::list',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});