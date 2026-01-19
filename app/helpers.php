<?php

use WPBulgaria\Chatbot\Application;
use WPBulgaria\Chatbot\Container\Container;

defined('ABSPATH') || exit;

if (!function_exists('wpb_chatbot_app')) {
    /**
     * Get the application instance or resolve a service
     * 
     * @param string|null $abstract Service to resolve, or null for the app instance
     * @param array $parameters Parameters to pass to the service constructor
     * @return mixed
     */
    function wpb_chatbot_app(?string $abstract = null, array $parameters = []): mixed {
        $app = Application::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->make($abstract, $parameters);
    }
}

if (!function_exists('wpb_chatbot_container')) {
    /**
     * Get the container instance
     */
    function wpb_chatbot_container(): Container {
        return Container::getInstance();
    }
}

if (!function_exists('wpb_chatbot_resolve')) {
    /**
     * Resolve a service from the container
     */
    function wpb_chatbot_resolve(string $abstract, array $parameters = []): mixed {
        return wpb_chatbot_container()->make($abstract, $parameters);
    }
}
