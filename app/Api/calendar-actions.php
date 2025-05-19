<?php

defined( 'ABSPATH' ) || exit;

add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/calendar', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\CalendarAction::list',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});


add_action('rest_api_init', function () {
    register_rest_route('experts-crm/v1', '/calendar/(?P<id>.+)', array(
        'methods' => 'GET',
        'callback' => 'ExpertsCrm\Actions\CalendarAction::listByObjectId',
        'permission_callback' => function () {
            return  \ExpertsCrm\authorize();
        }
    ));
});