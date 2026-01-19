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

    public function list($userId = 0): bool {
        if ($this->isAdminsOnly()) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to list chats'));
            });
        }

        if (!empty($userId) && $userId > 0 && !$this->currentUserCan('edit_others_posts')) {
            return $this->check($userId === $this->currentUserId(), function() {
                $this->setError(new AuthError('invalid_user', 'You are not allowed to list chats for this user'));
            });
        }

        return $this->check($this->currentUserCan('edit_others_posts'), function() {
            $this->setError(new AuthError('edit_others_posts', 'You are not allowed to list chats for other users'));
        });
    }

    public function get(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to get chat'));
            });
        }
        return $this->check($this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id), function() {
            $this->setError(new AuthError('edit_others_posts', 'You are not allowed to get chat for other users'));
        });
    }

    public function store(): bool {
        if ($this->isAdminsOnly()) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to store chat'));
            });
        }

        if (!$this->planService) {
            return true;
        }

        if ($this->planService->isGlobalChatsLimitReached()) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly chat limit for this service has been reached.'));
            return false;
        }

        return $this->check($this->planService->canCreateChat($this->currentUserId()), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for starting new chats.'));
        });
    }

    public function chat(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to chat'));
            });
        }

        if (!$this->planService) {
            return true;
        }

        if ($this->planService->isGlobalQuestionsLimitReached()) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly questions limit for this service has been reached.'));
            return false;
        }

        return $this->check($this->store() && $this->planService->canAskQuestion($this->currentUserId()), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan.'));
        });
    }

    public function stream(int|string|null $id = null): bool {
        if ($this->isAdminsOnly()) {
            return $this->check($this->currentUserCan('manage_options'), function() {
                $this->setError(new AuthError('manage_options', 'You are not allowed to stream chat '));
            });
        }

        if (!$this->planService) {
            return true;
        }

        if ($this->planService->isGlobalQuestionsLimitReached()) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly questions limit for this service has been reached.'));
            return false;
        }

        return $this->check($this->store() && $this->planService->canAskQuestion($this->currentUserId()), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan.'));
        });
    }

    public function canAnnonAskQuestion(int $currentChatMessageCount): bool {
        if (!$this->planService) {
            return true;
        }

        if ($this->planService->isGlobalQuestionsLimitReached()) {
            $this->setError(new AuthError('global_limit_reached', 'The monthly questions limit for this service has been reached.'));
            return false;
        }

        return $this->check($this->planService->canAnnonAskQuestion($currentChatMessageCount), function() {
            $this->setError(new AuthError('plan_limit_reached', 'You have reached the limit of your plan for asking questions.'));
        });
    }

    /**
     * Check if question message size is allowed by plan
     */
    public function validateQuestionSize(string $message): bool {
        if (!$this->planService) {
            return true;
        }

        return $this->check($this->planService->isQuestionSizeAllowed($this->currentUserId(), $message), function() {
            $this->setError(new AuthError('question_size_limit_reached', 'You have reached the limit of your plan because of too long question'));
        });
    }

    /**
     * Get user's plan usage summary
     */
    public function getUsageSummary(): array {
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

        return $this->planService->getUsageSummary($this->currentUserId());
    }

    /**
     * Get history size limit for current user
     */
    public function getHistorySize(): int {
        if (!$this->planService) {
            return -1;
        }

        return $this->planService->getHistorySize($this->currentUserId());
    }

    public function updateTitle(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }

    public function trash(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('delete_others_posts') || $this->currentUserCan('delete_post', $id);
    }

    public function remove(int|string $id): bool {
        if ($this->isAdminsOnly()) {
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('delete_others_posts') || $this->currentUserCan('delete_post', $id);
    }

    public function restore(int|string $id): bool {
        if ($this->isAdminsOnly()) {    
            return $this->currentUserCan('manage_options');
        }
        return $this->currentUserCan('edit_others_posts') || $this->currentUserCan('edit_post', $id);
    }
}
