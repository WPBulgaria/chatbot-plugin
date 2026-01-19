<?php

namespace WPBulgaria\Chatbot\Contracts;

use WPBulgaria\Chatbot\Container\Container;

defined('ABSPATH') || exit;

/**
 * Interface for service providers
 */
interface ServiceProviderInterface {

    /**
     * Register services in the container
     */
    public function register(Container $container): void;

    /**
     * Bootstrap services after all providers are registered
     */
    public function boot(Container $container): void;
}
