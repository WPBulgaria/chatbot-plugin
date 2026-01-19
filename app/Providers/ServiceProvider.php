<?php

namespace WPBulgaria\Chatbot\Providers;

use WPBulgaria\Chatbot\Container\Container;
use WPBulgaria\Chatbot\Contracts\ServiceProviderInterface;

defined('ABSPATH') || exit;

/**
 * Abstract base service provider
 */
abstract class ServiceProvider implements ServiceProviderInterface {

    /**
     * The container instance
     */
    protected Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Register services - override in child classes
     */
    public function register(Container $container): void {
        // Override in child classes
    }

    /**
     * Boot services - override in child classes
     */
    public function boot(Container $container): void {
        // Override in child classes
    }
}
