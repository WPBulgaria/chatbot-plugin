<?php

namespace WPBulgaria\Chatbot;

use WPBulgaria\Chatbot\Container\Container;
use WPBulgaria\Chatbot\Contracts\ServiceProviderInterface;

defined('ABSPATH') || exit;

/**
 * Main Application class - bootstraps the plugin
 */
class Application {

    /**
     * Singleton instance
     */
    private static ?Application $instance = null;

    /**
     * The service container
     */
    private Container $container;

    /**
     * Registered service providers
     * @var ServiceProviderInterface[]
     */
    private array $providers = [];

    /**
     * Whether the application has been booted
     */
    private bool $booted = false;

    /**
     * Service providers to register
     * @var class-string<ServiceProviderInterface>[]
     */
    private array $serviceProviders = [
        \WPBulgaria\Chatbot\Providers\AppServiceProvider::class,
    ];

    private function __construct() {
        $this->container = Container::getInstance();
        $this->container->instance(Application::class, $this);
        $this->container->instance(Container::class, $this->container);
    }

    /**
     * Get the application instance
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Boot the application
     */
    public function boot(): self {
        if ($this->booted) {
            return $this;
        }

        // Register all service providers
        foreach ($this->serviceProviders as $providerClass) {
            $this->registerProvider($providerClass);
        }

        // Boot all service providers
        foreach ($this->providers as $provider) {
            $provider->boot($this->container);
        }

        $this->booted = true;

        return $this;
    }

    /**
     * Register a service provider
     * 
     * @param class-string<ServiceProviderInterface> $providerClass
     */
    public function registerProvider(string $providerClass): ServiceProviderInterface {
        $provider = new $providerClass($this->container);
        $provider->register($this->container);
        $this->providers[] = $provider;

        return $provider;
    }

    /**
     * Get the container
     */
    public function getContainer(): Container {
        return $this->container;
    }

    /**
     * Resolve a service from the container
     */
    public function make(string $abstract, array $parameters = []): mixed {
        return $this->container->make($abstract, $parameters);
    }

    /**
     * Get a service from the container (alias for make)
     */
    public function get(string $abstract): mixed {
        return $this->container->get($abstract);
    }

    /**
     * Check if a service is bound
     */
    public function has(string $abstract): bool {
        return $this->container->has($abstract);
    }

    /**
     * Check if the application has booted
     */
    public function isBooted(): bool {
        return $this->booted;
    }
}

/**
 * Helper function to get the application instance
 */
function app(?string $abstract = null, array $parameters = []): mixed {
    $app = Application::getInstance();

    if ($abstract === null) {
        return $app;
    }

    return $app->make($abstract, $parameters);
}
