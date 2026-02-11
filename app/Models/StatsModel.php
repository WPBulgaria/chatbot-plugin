<?php

namespace WPBulgaria\Chatbot\Models;

defined('ABSPATH') || exit;

class StatsModel extends BaseModel {

    protected ChatModel $chatModel;

    public function __construct(ChatModel $chatModel) {
        parent::__construct();
        $this->chatModel = $chatModel;
    }

    /**
     * Get chat statistics for a specific period
     */
    public function getChatStats(string $period = 'all', ?int $chatbotId = null): array {
        global $wpdb;

        $dateWhere = $this->buildDateWhere($period);
        $chatbotWhere = $chatbotId ? $wpdb->prepare("AND post_parent = %d", $chatbotId) : '';

        $query = $wpdb->prepare(
            "SELECT 
                COUNT(DISTINCT ID) as total_chats,
                COUNT(DISTINCT post_author) as unique_users
            FROM {$wpdb->posts}
            WHERE post_type = %s
            AND post_status = 'publish'
            {$dateWhere}
            {$chatbotWhere}",
            ChatModel::POST_TYPE
        );

        $results = $wpdb->get_row($query, ARRAY_A);

        $totalChats = (int) ($results['total_chats'] ?? 0);
        $uniqueUsers = (int) ($results['unique_users'] ?? 0);
        $totalQuestions = $this->getTotalQuestions($period, $chatbotId);
        $avgQuestionsPerChat = $totalChats > 0 ? round($totalQuestions / $totalChats, 2) : 0;

        return [
            'period' => $period,
            'total_chats' => $totalChats,
            'total_questions' => $totalQuestions,
            'unique_users' => $uniqueUsers,
            'avg_questions_per_chat' => $avgQuestionsPerChat,
        ];
    }

    /**
     * Get total questions asked in a period
     */
    public function getTotalQuestions(string $period = 'all', ?int $chatbotId = null): int {
        global $wpdb;

        $dateWhere = $this->buildDateWhere($period);
        $chatbotWhere = $chatbotId ? $wpdb->prepare("AND post_parent = %d", $chatbotId) : '';

        $query = $wpdb->prepare(
            "SELECT ID
            FROM {$wpdb->posts}
            WHERE post_type = %s
            AND post_status = 'publish'
            {$dateWhere}
            {$chatbotWhere}",
            ChatModel::POST_TYPE
        );

        $chatIds = $wpdb->get_col($query);

        if (empty($chatIds)) {
            return 0;
        }

        $totalQuestions = 0;
        foreach ($chatIds as $chatId) {
            $messages = $this->chatModel->getMessages($chatId);
            if (is_array($messages)) {
                $userMessages = array_filter($messages, function($msg) {
                    return isset($msg['role']) && $msg['role'] === 'user';
                });
                $totalQuestions += count($userMessages);
            }
        }

        return $totalQuestions;
    }

    /**
     * Get comprehensive global statistics
     */
    public function getGlobalStats(?int $chatbotId = null): array {
        $allTimeStats = $this->getChatStats('all', $chatbotId);
        $dayStats = $this->getChatStats('day', $chatbotId);
        $weekStats = $this->getChatStats('week', $chatbotId);
        $monthStats = $this->getChatStats('month', $chatbotId);
        $yearStats = $this->getChatStats('year', $chatbotId);

        return [
            'all_time' => $allTimeStats,
            'today' => $dayStats,
            'this_week' => $weekStats,
            'this_month' => $monthStats,
            'this_year' => $yearStats,
            'summary' => [
                'total_chats_all_time' => $allTimeStats['total_chats'],
                'total_questions_all_time' => $allTimeStats['total_questions'],
                'unique_users_all_time' => $allTimeStats['unique_users'],
                'avg_questions_per_chat' => $allTimeStats['avg_questions_per_chat'],
                'chats_created_today' => $dayStats['total_chats'],
                'questions_asked_today' => $dayStats['total_questions'],
            ]
        ];
    }

    /**
     * Get stats by period comparison (current vs previous)
     */
    public function getComparativeStats(string $period = 'week', ?int $chatbotId = null): array {
        $currentStats = $this->getChatStats($period, $chatbotId);
        $previousStats = $this->getChatStats('previous_' . $period, $chatbotId);

        $chatGrowth = $this->calculateGrowth(
            $previousStats['total_chats'],
            $currentStats['total_chats']
        );

        $questionGrowth = $this->calculateGrowth(
            $previousStats['total_questions'],
            $currentStats['total_questions']
        );

        return [
            'current' => $currentStats,
            'previous' => $previousStats,
            'growth' => [
                'chats' => $chatGrowth,
                'questions' => $questionGrowth,
            ]
        ];
    }

    /**
     * Get activity chart data (for graphs)
     */
    public function getActivityChart(string $period = 'week', ?int $chatbotId = null): array {
        global $wpdb;

        $format = $this->getDateFormat($period);
        $dateWhere = $this->buildDateWhere($period);
        $chatbotWhere = $chatbotId ? $wpdb->prepare("AND post_parent = %d", $chatbotId) : '';

        $query = $wpdb->prepare(
            "SELECT 
                DATE_FORMAT(post_date, %s) as date_label,
                COUNT(*) as count,
                DATE(post_date) as date_key
            FROM {$wpdb->posts}
            WHERE post_type = %s
            AND post_status = 'publish'
            {$dateWhere}
            {$chatbotWhere}
            GROUP BY date_key
            ORDER BY date_key ASC",
            $format,
            ChatModel::POST_TYPE
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return array_map(function($row) {
            return [
                'date' => $row['date_label'],
                'chats' => (int) $row['count'],
            ];
        }, $results);
    }

    /**
     * Get top users by chat count
     */
    public function getTopUsers(int $limit = 10, ?int $chatbotId = null): array {
        global $wpdb;

        $chatbotWhere = $chatbotId ? $wpdb->prepare("AND post_parent = %d", $chatbotId) : '';

        $query = $wpdb->prepare(
            "SELECT 
                post_author,
                COUNT(*) as chat_count
            FROM {$wpdb->posts}
            WHERE post_type = %s
            AND post_status = 'publish'
            {$chatbotWhere}
            GROUP BY post_author
            ORDER BY chat_count DESC
            LIMIT %d",
            ChatModel::POST_TYPE,
            $limit
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return array_map(function($row) {
            $userId = (int) $row['post_author'];
            $user = get_userdata($userId);
            
            return [
                'user_id' => $userId,
                'user_name' => $user ? $user->display_name : 'Unknown User',
                'user_email' => $user ? $user->user_email : '',
                'chat_count' => (int) $row['chat_count'],
            ];
        }, $results);
    }

    /**
     * Build WHERE clause for date filtering
     */
    private function buildDateWhere(string $period): string {
        global $wpdb;

        return match($period) {
            'day', 'today' => $wpdb->prepare(
                "AND post_date >= %s",
                date('Y-m-d 00:00:00')
            ),
            'week', 'this_week' => $wpdb->prepare(
                "AND post_date >= %s",
                date('Y-m-d 00:00:00', strtotime('monday this week'))
            ),
            'month', 'this_month' => $wpdb->prepare(
                "AND post_date >= %s",
                date('Y-m-01 00:00:00')
            ),
            'year', 'this_year' => $wpdb->prepare(
                "AND post_date >= %s",
                date('Y-01-01 00:00:00')
            ),
            'previous_day' => $wpdb->prepare(
                "AND post_date >= %s AND post_date < %s",
                date('Y-m-d 00:00:00', strtotime('-1 day')),
                date('Y-m-d 00:00:00')
            ),
            'previous_week' => $wpdb->prepare(
                "AND post_date >= %s AND post_date < %s",
                date('Y-m-d 00:00:00', strtotime('monday last week')),
                date('Y-m-d 00:00:00', strtotime('monday this week'))
            ),
            'previous_month' => $wpdb->prepare(
                "AND post_date >= %s AND post_date < %s",
                date('Y-m-01 00:00:00', strtotime('first day of last month')),
                date('Y-m-01 00:00:00')
            ),
            'previous_year' => $wpdb->prepare(
                "AND post_date >= %s AND post_date < %s",
                date('Y-01-01 00:00:00', strtotime('-1 year')),
                date('Y-01-01 00:00:00')
            ),
            default => '' // 'all' - no date restriction
        };
    }

    /**
     * Get date format for grouping
     */
    private function getDateFormat(string $period): string {
        return match($period) {
            'day', 'today' => '%Y-%m-%d %H:00',
            'week', 'this_week' => '%Y-%m-%d',
            'month', 'this_month' => '%Y-%m-%d',
            'year', 'this_year' => '%Y-%m',
            default => '%Y-%m-%d'
        };
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth(int $previous, int $current): array {
        if ($previous === 0) {
            $percentage = $current > 0 ? 100 : 0;
        } else {
            $percentage = round((($current - $previous) / $previous) * 100, 2);
        }

        return [
            'value' => $percentage,
            'trend' => $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'stable'),
        ];
    }
}
