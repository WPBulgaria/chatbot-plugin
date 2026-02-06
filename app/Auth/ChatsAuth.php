<?php

namespace WPBulgaria\Chatbot\Auth;

defined('ABSPATH') || exit;

use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Services\PlanService;
use WPBulgaria\Chatbot\DataObjects\Auth\AuthError;

class ChatsAuth extends BaseAuth {

    protected ?PlanService $planService;

    public function __construct(ConfigsModel $configsModel, ?PlanService $planService = null) {
        parent::__construct($configsModel);
        $this->planService = $planService;
    }

    public function list(int|string $userId, ...$args): bool {
        if ($this->isAdminsOnly($args[0] ?? 0)) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to list chats'));
            });
        }

        if (!empty($userId) && $userId > 0 && !$this->userCan($userId, 'edit_others_posts')) {
            return $this->check($userId === $this->currentUserId(), function() {
                $this->setError(new AuthError('invalid_user', 'You are not allowed to list chats for this user'));
            });
        }

        return $this->check($this->userCan($userId, 'edit_others_posts'), function() {
            $this->setError(new AuthError('edit_others_posts', 'You are not allowed to list chats for other users'));
        });
    }

    public function get(int|string $userId, int|string $id, ...$args): bool {
        if ($this->isAdminsOnly($args[0] ?? 0)) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to get chat'));
            });
        }
        return $this->check($this->userCan($userId, 'edit_others_posts') || $this->currentUserCan('edit_post', $id), function() {
            $this->setError(new AuthError('edit_others_posts', 'You are not allowed to get chat for other users'));
        });
    }

    public function store(int|string $userId, ...$args): bool {
        $chatbotId = $args[0] ?? 0;
        if ($this->isAdminsOnly($chatbotId)) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to store chat'));
            });
        }

        if (!$this->planService) {
            return true;
        }

        // Check global limits first (fail-fast with cached count)
        if ($this->planService->isGlobalChatsLimitReached($chatbotId)) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly chat limit for this service has been reached.'));
            return false;
        }

        // Fetch plan once and pass to canCreateChat
        $plan = $this->planService->getCachedUserPlan($userId, $chatbotId);
        return $this->check($this->planService->canCreateChat($userId, $chatbotId, $plan), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for starting new chats.'));
        });
    }

    public function chat(int|string $userId = 0, int|string|null $id = null, int|string $chatbotId = 0): bool {
        if ($this->isAdminsOnly($chatbotId)) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to chat'));
            });
        }

        if (!$this->planService) {
            return true;
        }

        // Check global limits first (fail-fast)
        if ($this->planService->isGlobalChatsLimitReached($chatbotId)) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly chat limit for this service has been reached.'));
            return false;
        }

        // Fetch plan once and reuse for both checks
        $plan = $this->planService->getCachedUserPlan($userId, $chatbotId);
        
        if (!$this->planService->canCreateChat($userId, $chatbotId, $plan)) {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for starting new chats.'));
            return false;
        }

        return $this->check($this->planService->canAskQuestion($userId, $chatbotId, $plan), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan.'));
        });
    }

    public function stream(int|string $userId = 0, int|string|null $id = null, int|string $chatbotId = 0): bool {
        if ($this->isAdminsOnly($chatbotId)) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to stream chat '));
            });
        }

        if (!$this->planService) {
            return true;
        }
        
        // Check global limits first (fail-fast with cached count)
        if ($this->planService->isGlobalChatsLimitReached($chatbotId)) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly chat limit for this service has been reached.'));
            return false;
        }

        // Fetch plan once and reuse for both checks
        $plan = $this->planService->getCachedUserPlan($userId, $chatbotId);
        
        if (!$this->planService->canCreateChat($userId, $chatbotId, $plan)) {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for starting new chats.'));
            return false;
        }

        return $this->check($this->planService->canAskQuestion($userId, $chatbotId, $plan), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan.'));
        });
    }

    public function canAnnonAskQuestion(int $currentChatMessageCount, int|string $chatbotId = 0): bool {
        if (!$this->planService) {
            return true;
        }

        if ($this->planService->isGlobalChatsLimitReached($chatbotId)) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly questions limit for this service has been reached.'));
            return false;
        }

        return $this->check($this->planService->canAnnonAskQuestion($currentChatMessageCount, $chatbotId), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for asking questions.'));
        });
    }

    /**
     * Check if question message size is allowed by plan
     * Uses cached plan for better performance
     */
    public function validateQuestionSize(int|string $userId, string $message, int|string $chatbotId = 0): bool {
        if (!$this->planService) {
            return true;
        }

        $plan = $this->planService->getCachedUserPlan($userId, $chatbotId);
        return $this->check($this->planService->isQuestionSizeAllowed($userId, $chatbotId, $message, $plan), function() {
            $this->setError(new AuthError('question_size_limit_reached', 'You have reached the limit of your plan because of too long question'));
        });
    }

    /**
     * Get user's plan usage summary
     */
    public function getUsageSummary(int|string $userId = 0, int|string $chatbotId = 0): array {
        if (!$this->planService) {
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

        return $this->planService->getUsageSummary($userId, $chatbotId);
    }

    /**
     * Get history size limit for current user
     */
    public function getHistorySize(int|string $userId = 0, int|string $chatbotId = 0): int {
        if (!$this->planService) {
            return -1;
        }

        return $this->planService->getHistorySize($userId, $chatbotId);
    }

    public function updateTitle(int|string $userId, int|string $id, int|string $chatbotId = 0): bool {
        if ($this->isAdminsOnly($chatbotId)) {
            return $this->currentUserCan('manage_options');
        }
        return $this->userCan($userId, 'edit_others_posts') || $this->userCan($userId, 'edit_post', $id);
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        if ($this->isAdminsOnly($args[0] ?? 0)) {
            return $this->currentUserCan('manage_options');
        }
        return $this->userCan($userId, 'delete_others_posts') || $this->userCan($userId, 'delete_post', $id);
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        if ($this->isAdminsOnly($args[0] ?? 0)) {
            return $this->currentUserCan('manage_options');
        }
        return $this->userCan($userId, 'delete_others_posts') || $this->userCan($userId, 'delete_post', $id);
    }

    public function restore(int|string $userId, int|string $id, ...$args): bool {
        if ($this->isAdminsOnly($args[0] ?? 0)) {    
            return $this->currentUserCan('manage_options');
        }
        return $this->userCan($userId, 'edit_others_posts') || $this->userCan($userId, 'edit_post', $id);
    }

    public function saveMessage(int|string $userId, int|string $id, int|string $chatbotId = 0): bool {
        if ($this->isAdminsOnly($chatbotId)) {
            return $this->currentUserCan('manage_options');
        }
        return $this->userCan($userId, 'edit_others_posts');
    }
}
