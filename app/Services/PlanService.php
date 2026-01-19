<?php

namespace WPBulgaria\Chatbot\Services;

use WPBulgaria\Chatbot\Models\PlanModel;
use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Models\PostModel;
use WPBulgaria\Chatbot\Models\ChatModel;
use WPBulgaria\Chatbot\Enums\PlanPeriods;
use function WPBulgaria\Chatbot\Functions\mb_str_word_count;

defined('ABSPATH') || exit;

/**
 * Service for plan-based authorization and limits checking
 */
class PlanService {

    const UNLIMITED = -1;

    protected PlanModel $planModel;
    protected ConfigsModel $configsModel;
    protected PostModel $postModel;

    public function __construct(
        PlanModel $planModel,
        ConfigsModel $configsModel,
        PostModel $postModel
    ) {
        $this->planModel = $planModel;
        $this->configsModel = $configsModel;
        $this->postModel = $postModel;
    }

    /**
     * Get the effective plan for a user
     * Priority: User's assigned plan > Default plan > null
     */
    public function getUserPlan(int $userId): ?array {
        if ($userId <= 0) {
            return $this->getPublicPlan();
        }

        $userPlanId = $this->planModel->getUserPlanId($userId);
        if ($userPlanId) {
            $plan = $this->planModel->get($userPlanId);
            if ($plan) {
                return $plan;
            }
        }

        return $this->getDefaultPlan();
    }

    /**
     * Get the public plan for non-logged-in users
     */
    public function getPublicPlan(): ?array {
        $configs = $this->configsModel->view();
        $publicPlanId = $configs["publicPlan"] ?? "";

        if (empty($publicPlanId)) {
            return null;
        }

        return $this->planModel->get($publicPlanId);
    }

    /**
     * Get the default plan for logged-in users without assigned plan
     */
    public function getDefaultPlan(): ?array {
        $configs = $this->configsModel->view();
        $defaultPlanId = $configs["defaultPlan"] ?? "";

        if (empty($defaultPlanId)) {
            return null;
        }

        return $this->planModel->get($defaultPlanId);
    }

    /**
     * Check if value represents unlimited
     */
    public function isUnlimited(int $value): bool {
        return $value === self::UNLIMITED;
    }

    /**
     * Get the period start date based on plan period
     */
    public function getPeriodStartDate(string $period): string {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        switch ($period) {
            case PlanPeriods::DAY->value:
                return $now->format('Y-m-d 00:00:00');

            case PlanPeriods::WEEK->value:
                $now->modify('monday this week');
                return $now->format('Y-m-d 00:00:00');

            case PlanPeriods::MONTH->value:
                return $now->format('Y-m-01 00:00:00');

            case PlanPeriods::YEAR->value:
                return $now->format('Y-01-01 00:00:00');

            case PlanPeriods::LIFETIME->value:
            default:
                return '1970-01-01 00:00:00';
        }
    }

    /**
     * Count user's chats within the plan period
     */
    public function countUserChatsInPeriod(int $userId, string $period): int {
        if ($userId <= 0) {
            return 0;
        }

        $periodStart = $this->getPeriodStartDate($period);

        $args = [
            'post_type'      => ChatModel::POST_TYPE,
            'post_status'    => ['publish', 'trash'],
            'author'         => $userId,
            'date_query'     => [
                [
                    'after'     => $periodStart,
                    'inclusive' => true,
                ]
            ],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query = new \WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Count ALL chats globally within the current month
     */
    public function countGlobalChatsThisMonth(): int {
        $periodStart = $this->getPeriodStartDate(PlanPeriods::MONTH->value);

        $args = [
            'post_type'      => ChatModel::POST_TYPE,
            'post_status'    => ['publish', 'trash'],
            'date_query'     => [
                [
                    'after'     => $periodStart,
                    'inclusive' => true,
                ]
            ],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query = new \WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Count ALL questions globally within the current month
     */
    public function countGlobalQuestionsThisMonth(): int {
        $periodStart = $this->getPeriodStartDate(PlanPeriods::MONTH->value);

        $args = [
            'post_type'      => ChatModel::POST_TYPE,
            'post_status'    => ['publish'],
            'date_query'     => [
                [
                    'after'     => $periodStart,
                    'inclusive' => true,
                ]
            ],
            'posts_per_page' => -1,
        ];

        $query = new \WP_Query($args);
        $totalQuestions = 0;

        foreach ($query->posts as $post) {
            $messages = $this->postModel->getMeta($post->ID, ChatModel::META_MESSAGES);
            if (is_array($messages)) {
                $userMessages = array_filter($messages, fn($msg) => ($msg['role'] ?? '') === 'user');
                $totalQuestions += count($userMessages);
            }
        }

        return $totalQuestions;
    }

    /**
     * Get global monthly chat limit from configs
     */
    public function getGlobalChatsLimit(): int {
        $configs = $this->configsModel->view();
        return (int) ($configs["totalChats"] ?? 0);
    }

    /**
     * Get global monthly questions limit from configs
     */
    public function getGlobalQuestionsLimit(): int {
        $configs = $this->configsModel->view();
        return (int) ($configs["totalQuestions"] ?? 0);
    }

    /**
     * Check if global monthly chat limit is reached
     */
    public function isGlobalChatsLimitReached(): bool {
        $limit = $this->getGlobalChatsLimit();

        if ($this->isUnlimited($limit) || $limit <= 0) {
            return false;
        }

        return $this->countGlobalChatsThisMonth() >= $limit;
    }

    /**
     * Check if global monthly questions limit is reached
     */
    public function isGlobalQuestionsLimitReached(): bool {
        $limit = $this->getGlobalQuestionsLimit();

        if ($this->isUnlimited($limit) || $limit <= 0) {
            return false;
        }

        return $this->countGlobalQuestionsThisMonth() >= $limit;
    }

    /**
     * Get remaining global chats this month
     */
    public function getRemainingGlobalChats(): int {
        $limit = $this->getGlobalChatsLimit();

        if ($this->isUnlimited($limit) || $limit <= 0) {
            return self::UNLIMITED;
        }

        return max(0, $limit - $this->countGlobalChatsThisMonth());
    }

    /**
     * Get remaining global questions this month
     */
    public function getRemainingGlobalQuestions(): int {
        $limit = $this->getGlobalQuestionsLimit();

        if ($this->isUnlimited($limit) || $limit <= 0) {
            return self::UNLIMITED;
        }

        return max(0, $limit - $this->countGlobalQuestionsThisMonth());
    }

    /**
     * Count user's questions (messages) within the plan period
     */
    public function countUserQuestionsInPeriod(int $userId, string $period): int {
        if ($userId <= 0) {
            return 0;
        }

        $periodStart = $this->getPeriodStartDate($period);

        $args = [
            'post_type'      => ChatModel::POST_TYPE,
            'post_status'    => ['publish'],
            'author'         => $userId,
            'date_query'     => [
                [
                    'after'     => $periodStart,
                    'inclusive' => true,
                ]
            ],
            'posts_per_page' => -1,
        ];

        $query = new \WP_Query($args);
        $totalQuestions = 0;

        foreach ($query->posts as $post) {
            $messages = $this->postModel->getMeta($post->ID, ChatModel::META_MESSAGES);
            if (is_array($messages)) {
                $userMessages = array_filter($messages, fn($msg) => ($msg['role'] ?? '') === 'user');
                $totalQuestions += count($userMessages);
            }
        }

        return $totalQuestions;
    }

    /**
     * Check if user can create a new chat
     */
    public function canCreateChat(int $userId): bool {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return false;
        }

        $totalChats = $plan["totalChats"] ?? 0;

        if ($this->isUnlimited($totalChats)) {
            return true;
        }

        $period = $plan["period"] ?? PlanPeriods::MONTH->value;
        $currentChats = $this->countUserChatsInPeriod($userId, $period);

        return $currentChats < $totalChats;
    }

    public function canAnnonAskQuestion(int $currentChatMessageCount): bool {
        $plan = $this->getPublicPlan();
        
        if (!$plan) {
            return false;
        }

        $totalQuestions = $plan["totalQuestions"] ?? 0;

        if ($this->isUnlimited($totalQuestions)) {
            return true;
        }

        return $currentChatMessageCount < $totalQuestions;
    }

    /**
     * Check if user can ask a new question
     */
    public function canAskQuestion(int $userId): bool {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return false;
        }

        $totalQuestions = $plan["totalQuestions"] ?? 0;

        if ($this->isUnlimited($totalQuestions)) {
            return true;
        }

        $period = $plan["period"] ?? PlanPeriods::MONTH->value;
        $currentQuestions = $this->countUserQuestionsInPeriod($userId, $period);

        return $currentQuestions < $totalQuestions;
    }

    /**
     * Check if question length is within plan limit
     */
    public function isQuestionSizeAllowed(int $userId, string $question): bool {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return false;
        }

        $questionSize = $plan["questionSize"] ?? 0;

        if ($this->isUnlimited($questionSize)) {
            return true;
        }

        return mb_str_word_count($question) <= $questionSize;
    }

    /**
     * Get allowed history size for user's plan
     */
    public function getHistorySize(int $userId): int {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return 0;
        }

        return $plan["historySize"] ?? 0;
    }

    /**
     * Get remaining chats for user
     */
    public function getRemainingChats(int $userId): int {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return 0;
        }

        $totalChats = $plan["totalChats"] ?? 0;

        if ($this->isUnlimited($totalChats)) {
            return self::UNLIMITED;
        }

        $period = $plan["period"] ?? PlanPeriods::MONTH->value;
        $currentChats = $this->countUserChatsInPeriod($userId, $period);

        return max(0, $totalChats - $currentChats);
    }

    /**
     * Get remaining questions for user
     */
    public function getRemainingQuestions(int $userId): int {
        $plan = $this->getUserPlan($userId);
        
        if (!$plan) {
            return 0;
        }

        $totalQuestions = $plan["totalQuestions"] ?? 0;

        if ($this->isUnlimited($totalQuestions)) {
            return self::UNLIMITED;
        }

        $period = $plan["period"] ?? PlanPeriods::MONTH->value;
        $currentQuestions = $this->countUserQuestionsInPeriod($userId, $period);

        return max(0, $totalQuestions - $currentQuestions);
    }

    public function getGlobalUsageSummary(): array {
        return [
            'globalChatsUsed'          => $this->countGlobalChatsThisMonth(),
            'globalChatsTotal'         => $this->getGlobalChatsLimit(),
            'globalChatsRemaining'     => $this->getRemainingGlobalChats(),
            'globalQuestionsUsed'      => $this->countGlobalQuestionsThisMonth(),
            'globalQuestionsTotal'     => $this->getGlobalQuestionsLimit(),
            'globalQuestionsRemaining' => $this->getRemainingGlobalQuestions(),
        ];
    }

    /**
     * Get user's plan usage summary
     */
    public function getUsageSummary(int $userId): array {
        $plan = $this->getUserPlan($userId);

        if (!$plan) {
            return [
                'hasPlan'            => false,
                'planName'           => null,
                'period'             => null,
                'chatsUsed'          => 0,
                'chatsTotal'         => 0,
                'chatsRemaining'     => 0,
                'questionsUsed'      => 0,
                'questionsTotal'     => 0,
                'questionsRemaining' => 0,
                'historySize'        => 0,
                'questionSize'       => 0,
            ];
        }

        $period = $plan["period"] ?? PlanPeriods::MONTH->value;
        $chatsUsed = $this->countUserChatsInPeriod($userId, $period);
        $questionsUsed = $this->countUserQuestionsInPeriod($userId, $period);

        return [
            'hasPlan'            => true,
            'planName'           => $plan["name"] ?? "Unknown",
            'planId'             => $plan["id"] ?? null,
            'period'             => $period,
            'chatsUsed'          => $chatsUsed,
            'chatsTotal'         => $plan["totalChats"] ?? 0,
            'chatsRemaining'     => $this->getRemainingChats($userId),
            'questionsUsed'      => $questionsUsed,
            'questionsTotal'     => $plan["totalQuestions"] ?? 0,
            'questionsRemaining' => $this->getRemainingQuestions($userId),
            'historySize'        => $plan["historySize"] ?? 0,
            'questionSize'       => $plan["questionSize"] ?? 0,
        ];
    }
}
