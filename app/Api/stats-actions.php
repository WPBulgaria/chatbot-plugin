<?php

defined('ABSPATH') || exit;

// GET /stats - Get statistics for a specific period
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/stats', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getPeriodStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getPeriodStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /stats/global - Get comprehensive global statistics
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/stats/global', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getGlobalStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getGlobalStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /stats/comparative - Get comparative statistics (current vs previous period)
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/stats/comparative', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getComparativeStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getComparativeStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /stats/chart - Get activity chart data
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/stats/chart', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getActivityChart',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getActivityChart(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /stats/top-users - Get top users by chat count
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/stats/top-users', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getTopUsers',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getTopUsers(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /chatbots/{chatbot_id}/stats - Get statistics for a specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/stats', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getPeriodStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getPeriodStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /chatbots/{chatbot_id}/stats/global - Get global statistics for a specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/stats/global', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getGlobalStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getGlobalStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /chatbots/{chatbot_id}/stats/comparative - Get comparative statistics for a specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/stats/comparative', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getComparativeStats',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getComparativeStats(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /chatbots/{chatbot_id}/stats/chart - Get activity chart data for a specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/stats/chart', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getActivityChart',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getActivityChart(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});

// GET /chatbots/{chatbot_id}/stats/top-users - Get top users for a specific chatbot
add_action('rest_api_init', function () {
    register_rest_route(WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/stats/top-users', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\StatsAction::getTopUsers',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\StatsAuthFactory::class);
            $result = $auth->getTopUsers(get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ));
});
