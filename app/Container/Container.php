<?php

namespace WPBulgaria\Chatbot\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionParameter;

defined('ABSPATH') || exit;

/**
 * Simple Dependency Injection Container
 */
class Container {

    /**
     * Singleton instance
     */
    private static ?Container $instance = null;

    /**
     * Registered bindings
     * @var array<string, Closure|string|object>
     */
    private array $bindings = [];

    /**
     * Singleton instances
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Aliases for bindings
     * @var array<string, string>
     */
    private array $aliases = [];

    /**
     * Get the singleton container instance
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set the singleton container instance (useful for testing)
     */
    public static function setInstance(?Container $container): void {
        self::$instance = $container;
    }

    /**
     * Register a binding in the container
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void {
        $concrete = $concrete ?? $abstract;

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    /**
     * Register a shared binding (singleton)
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as a singleton
     */
    public function instance(string $abstract, object $instance): object {
        $this->instances[$abstract] = $instance;
        return $instance;
    }

    /**
     * Register an alias for a binding
     */
    public function alias(string $abstract, string $alias): void {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Resolve a binding from the container
     */
    public function make(string $abstract, array $parameters = []): mixed {
        // Resolve alias
        $abstract = $this->getAlias($abstract);

        // Return existing instance if available
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get the concrete implementation
        $concrete = $this->getConcrete($abstract);

        // Build the instance
        $object = $this->build($concrete, $parameters);

        // Store as singleton if shared
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Get method - alias for make()
     */
    public function get(string $abstract): mixed {
        return $this->make($abstract);
    }

    /**
     * Check if a binding exists
     */
    public function has(string $abstract): bool {
        return isset($this->bindings[$this->getAlias($abstract)]) 
            || isset($this->instances[$this->getAlias($abstract)]);
    }

    /**
     * Check if a binding is bound
     */
    public function bound(string $abstract): bool {
        return $this->has($abstract);
    }

    /**
     * Resolve the alias
     */
    protected function getAlias(string $abstract): string {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Get the concrete implementation
     */
    protected function getConcrete(string $abstract): Closure|string {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Check if binding is shared (singleton)
     */
    protected function isShared(string $abstract): bool {
        return isset($this->bindings[$abstract]['shared']) 
            && $this->bindings[$abstract]['shared'] === true;
    }

    /**
     * Build a concrete instance
     */
    protected function build(Closure|string $concrete, array $parameters = []): object {
        // If it's a closure, execute it
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        // Use reflection to instantiate the class
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (\ReflectionException $e) {
            throw new Exception("Class {$concrete} does not exist.");
        }

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        // No constructor, just instantiate
        if ($constructor === null) {
            return new $concrete();
        }

        // Resolve constructor dependencies
        $dependencies = $this->resolveDependencies(
            $constructor->getParameters(),
            $parameters
        );

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve constructor dependencies
     * 
     * @param ReflectionParameter[] $dependencies
     */
    protected function resolveDependencies(array $dependencies, array $parameters = []): array {
        $resolved = [];

        foreach ($dependencies as $dependency) {
            $name = $dependency->getName();

            // Check if parameter was provided
            if (array_key_exists($name, $parameters)) {
                $resolved[] = $parameters[$name];
                continue;
            }

            // Try to resolve by type
            $type = $dependency->getType();

            if ($type === null || $type->isBuiltin()) {
                // Check for default value
                if ($dependency->isDefaultValueAvailable()) {
                    $resolved[] = $dependency->getDefaultValue();
                } else {
                    throw new Exception(
                        "Unable to resolve dependency [{$name}] in class"
                    );
                }
                continue;
            }

            // Resolve the typed dependency
            $typeName = $type->getName();
            $resolved[] = $this->make($typeName);
        }

        return $resolved;
    }

    /**
     * Call a method with dependency injection
     */
    public function call(callable|array|string $callback, array $parameters = []): mixed {
        if (is_string($callback) && str_contains($callback, '@')) {
            [$class, $method] = explode('@', $callback);
            $callback = [$this->make($class), $method];
        }

        if (is_array($callback) && is_string($callback[0])) {
            $callback[0] = $this->make($callback[0]);
        }

        return call_user_func_array($callback, $parameters);
    }

    /**
     * Flush the container (useful for testing)
     */
    public function flush(): void {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
    }
}
