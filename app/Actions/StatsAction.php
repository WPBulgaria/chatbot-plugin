<?php

namespace WPBulgaria\Chatbot\Actions;

use WPBulgaria\Chatbot\Models\StatsModel;
use WPBulgaria\Chatbot\Utils\ResponseCache;

defined('ABSPATH') || exit;

/**
 * Stats Action - handles statistics-related REST API endpoints with caching
 */
class StatsAction {

    /**
     * Get ResponseCache from container
     */
    protected static function getCache(): ResponseCache {
        return wpb_chatbot_app(ResponseCache::class);
    }

    /**
     * Get statistics for a specific period (with caching)
     */
    public static function getPeriodStats(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $period = $params['period'] ?? 'all';
        $chatbotId = isset($params['chatbot_id']) ? absint($params['chatbot_id']) : null;
        $skipCache = isset($params['skip_cache']) ? !!$params['skip_cache'] : false;

        $validPeriods = ['day', 'today', 'week', 'month', 'year', 'all'];
        if (!in_array($period, $validPeriods, true)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid period. Valid periods: " . implode(', ', $validPeriods)
            ], 400);
        }

        $cache = self::getCache();
        $cacheKey = $cache->generateKey('period_stats', [
            'period' => $period,
            'chatbot_id' => $chatbotId
        ]);

        if (!$skipCache && $cache->has($cacheKey)) {
            $stats = $cache->get($cacheKey);
            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                true,
                $cacheKey
            );
        }

        try {
            $stats = wpb_chatbot_app(StatsModel::class)->getChatStats($period, $chatbotId);
            $cache->set($cacheKey, $stats);

            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                false,
                $cacheKey
            );
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Get comprehensive global statistics (with caching)
     */
    public static function getGlobalStats(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $chatbotId = isset($params['chatbot_id']) ? absint($params['chatbot_id']) : null;
        $skipCache = isset($params['skip_cache']) ? !!$params['skip_cache'] : false;

        $cache = self::getCache();
        $cacheKey = $cache->generateKey('global_stats', [
            'chatbot_id' => $chatbotId
        ]);

        if (!$skipCache && $cache->has($cacheKey)) {
            $stats = $cache->get($cacheKey);
            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                true,
                $cacheKey
            );
        }

        try {
            $stats = wpb_chatbot_app(StatsModel::class)->getGlobalStats($chatbotId);
            $cache->set($cacheKey, $stats);

            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                false,
                $cacheKey
            );
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Get comparative statistics (current vs previous period, with caching)
     */
    public static function getComparativeStats(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $period = $params['period'] ?? 'week';
        $chatbotId = isset($params['chatbot_id']) ? absint($params['chatbot_id']) : null;
        $skipCache = isset($params['skip_cache']) ? !!$params['skip_cache'] : false;

        $validPeriods = ['day', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods, true)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid period. Valid periods: " . implode(', ', $validPeriods)
            ], 400);
        }

        $cache = self::getCache();
        $cacheKey = $cache->generateKey('comparative_stats', [
            'period' => $period,
            'chatbot_id' => $chatbotId
        ]);

        if (!$skipCache && $cache->has($cacheKey)) {
            $stats = $cache->get($cacheKey);
            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                true,
                $cacheKey
            );
        }

        try {
            $stats = wpb_chatbot_app(StatsModel::class)->getComparativeStats($period, $chatbotId);
            $cache->set($cacheKey, $stats);

            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "stats" => $stats
                ], 200),
                false,
                $cacheKey
            );
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Get activity chart data (with caching)
     */
    public static function getActivityChart(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $period = $params['period'] ?? 'week';
        $chatbotId = isset($params['chatbot_id']) ? absint($params['chatbot_id']) : null;
        $skipCache = isset($params['skip_cache']) ? !!$params['skip_cache'] : false;

        $validPeriods = ['day', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods, true)) {
            return new \WP_REST_Response([
                "success" => false,
                "message" => "Invalid period. Valid periods: " . implode(', ', $validPeriods)
            ], 400);
        }

        $cache = self::getCache();
        $cacheKey = $cache->generateKey('activity_chart', [
            'period' => $period,
            'chatbot_id' => $chatbotId
        ]);

        if (!$skipCache && $cache->has($cacheKey)) {
            $chartData = $cache->get($cacheKey);
            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "data" => $chartData
                ], 200),
                true,
                $cacheKey
            );
        }

        try {
            $chartData = wpb_chatbot_app(StatsModel::class)->getActivityChart($period, $chatbotId);
            $cache->set($cacheKey, $chartData);

            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "data" => $chartData
                ], 200),
                false,
                $cacheKey
            );
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }

    /**
     * Get top users by chat count (with caching)
     */
    public static function getTopUsers(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_params();
        $limit = isset($params['limit']) ? absint($params['limit']) : 10;
        $chatbotId = isset($params['chatbot_id']) ? absint($params['chatbot_id']) : null;
        $skipCache = isset($params['skip_cache']) ? !!$params['skip_cache'] : false;

        if ($limit > 100) {
            $limit = 100;
        }

        $cache = self::getCache();
        $cacheKey = $cache->generateKey('top_users', [
            'limit' => $limit,
            'chatbot_id' => $chatbotId
        ]);

        if (!$skipCache && $cache->has($cacheKey)) {
            $users = $cache->get($cacheKey);
            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "users" => $users
                ], 200),
                true,
                $cacheKey
            );
        }

        try {
            $users = wpb_chatbot_app(StatsModel::class)->getTopUsers($limit, $chatbotId);
            $cache->set($cacheKey, $users);

            return $cache->wrapResponse(
                new \WP_REST_Response([
                    "success" => true,
                    "users" => $users
                ], 200),
                false,
                $cacheKey
            );
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return new \WP_REST_Response([
                "success" => false,
                "message" => esc_html($e->getMessage())
            ], $code);
        }
    }
}
