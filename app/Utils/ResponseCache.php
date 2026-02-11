<?php

namespace WPBulgaria\Chatbot\Utils;

defined('ABSPATH') || exit;

/**
 * Response Cache Utility
 * Provides caching functionality for Action classes with automatic invalidation
 */
class ResponseCache {

    protected string $cacheGroup;
    protected int $defaultTtl;
    protected array $keyIndex = [];

    /**
     * Constructor
     */
    public function __construct(string $cacheGroup = 'wpb_chatbot', int $defaultTtl = 300) {
        $this->cacheGroup = $cacheGroup;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * Get cached data
     */
    public function get(string $key): mixed {
        $fullKey = $this->buildKey($key);
        return get_transient($fullKey);
    }

    /**
     * Set cached data
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool {
        $fullKey = $this->buildKey($key);
        $ttl = $ttl ?? $this->defaultTtl;
        return set_transient($fullKey, $value, $ttl);
    }

    /**
     * Delete cached data
     */
    public function delete(string $key): bool {
        $fullKey = $this->buildKey($key);
        return delete_transient($fullKey);
    }

    /**
     * Check if cache exists
     */
    public function has(string $key): bool {
        return $this->get($key) !== false;
    }

    /**
     * Get or set cache with callback
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed {
        $cached = $this->get($key);
        
        if ($cached !== false) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Clear all cache for this group
     */
    public function flush(): bool {
        global $wpdb;
        
        $pattern = $this->cacheGroup . '_%';
        $query = $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_' . $pattern
        );
        
        $wpdb->query($query);
        
        $query = $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_timeout_' . $pattern
        );
        
        return (bool) $wpdb->query($query);
    }

    /**
     * Build full cache key
     */
    protected function buildKey(string $key): string {
        return $this->cacheGroup . '_' . $key;
    }

    /**
     * Generate cache key from parameters
     */
    public function generateKey(string $prefix, array $params = []): string {
        if (empty($params)) {
            return $prefix;
        }

        ksort($params);
        $hash = md5(serialize($params));
        
        return sprintf('%s_%s', $prefix, $hash);
    }

    /**
     * Add cache key to index for group invalidation
     */
    public function addToIndex(string $indexKey, string $cacheKey): void {
        $index = $this->get($indexKey);
        
        if (!is_array($index)) {
            $index = [];
        }
        
        if (!in_array($cacheKey, $index, true)) {
            $index[] = $cacheKey;
            $this->set($indexKey, $index, $this->defaultTtl);
        }
    }

    /**
     * Invalidate all cache keys in an index
     */
    public function invalidateIndex(string $indexKey): void {
        $index = $this->get($indexKey);
        
        if (is_array($index)) {
            foreach ($index as $cacheKey) {
                $this->delete($cacheKey);
            }
        }
        
        $this->delete($indexKey);
    }

    /**
     * Invalidate multiple cache keys by pattern
     */
    public function invalidateByPattern(string $pattern): int {
        global $wpdb;
        
        $fullPattern = $this->cacheGroup . '_' . $pattern;
        $query = $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_' . $fullPattern
        );
        
        $results = $wpdb->get_col($query);
        $count = 0;
        
        foreach ($results as $optionName) {
            $key = str_replace('_transient_' . $this->cacheGroup . '_', '', $optionName);
            if ($this->delete($key)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Create a cache key for list endpoints
     */
    public function createListKey(string $resource, array $params = []): string {
        $defaults = [
            'user_id' => 0,
            'per_page' => 20,
            'page' => 1,
            'parent_id' => 0,
        ];
        
        $params = array_merge($defaults, $params);
        
        return $this->generateKey($resource . '_list', $params);
    }

    /**
     * Create a cache key for single resource
     */
    public function createResourceKey(string $resource, int $id, array $params = []): string {
        $params['id'] = $id;
        return $this->generateKey($resource, $params);
    }

    /**
     * Create index key for resource group
     */
    public function createIndexKey(string $resource, int $userId, int $parentId = 0): string {
        return sprintf('%s_index_%d_%d', $resource, $userId, $parentId);
    }

    /**
     * Wrap response with cache headers
     */
    public function wrapResponse(\WP_REST_Response $response, bool $isHit, string $key = ''): \WP_REST_Response {
        $response->header('X-Cache-Status', $isHit ? 'HIT' : 'MISS');
        $response->header('X-Cache-Group', $this->cacheGroup);
        
        if ($key) {
            $response->header('X-Cache-Key', $key);
        }
        
        return $response;
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array {
        global $wpdb;
        
        $pattern = $this->cacheGroup . '_%';
        $query = $wpdb->prepare(
            "SELECT COUNT(*) as total FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_' . $pattern
        );
        
        $total = (int) $wpdb->get_var($query);
        
        $query = $wpdb->prepare(
            "SELECT 
                SUM(LENGTH(option_value)) as size 
            FROM {$wpdb->options} 
            WHERE option_name LIKE %s",
            '_transient_' . $pattern
        );
        
        $size = (int) $wpdb->get_var($query);
        
        return [
            'total_keys' => $total,
            'total_size' => $size,
            'total_size_mb' => round($size / 1024 / 1024, 2),
            'cache_group' => $this->cacheGroup,
        ];
    }

    /**
     * Set cache TTL
     */
    public function setDefaultTtl(int $ttl): void {
        $this->defaultTtl = $ttl;
    }

    /**
     * Get cache TTL
     */
    public function getDefaultTtl(): int {
        return $this->defaultTtl;
    }

    /**
     * Set cache group
     */
    public function setCacheGroup(string $group): void {
        $this->cacheGroup = $group;
    }

    /**
     * Get cache group
     */
    public function getCacheGroup(): string {
        return $this->cacheGroup;
    }
}
