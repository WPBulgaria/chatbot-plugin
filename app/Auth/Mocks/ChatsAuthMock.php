<?php

namespace WPBulgaria\Chatbot\Auth\Mocks;

use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Services\PlanService;

defined('ABSPATH') || exit;

class ChatsAuthMock extends BaseAuthMock {

    protected ?PlanService $planService;

    public function __construct(ConfigsModel $configsModel, ?PlanService $planService = null) {
        parent::__construct($configsModel);
        $this->planService = $planService;
    }

    public function list(int|string $userId, ...$args): bool {
        return true;
    }

    public function get(int|string $userId, int|string $id, int|string $chatbotId = 0): bool {
        return true;        
    }

    public function store(int|string $userId, ...$args): bool {
        return true;
    }

    public function chat(int|string $userId, int|string|null $id = null, int|string $chatbotId = 0): bool {
        return true;
    }

    public function stream(int|string $userId, int|string|null $id = null, int|string $chatbotId = 0): bool {
        return true;
    }

    public function validateQuestionSize(int|string $userId, string $message, int|string $chatbotId = 0): bool {
        return true;
    }

    public function getUsageSummary(int|string $chatbotId = 0): array {
        return [
            'hasPlan'            => true,
            'planName'           => 'Mock Plan',
            'planId'             => 'mock-plan-id',
            'period'             => 'lifetime',
            'chatsUsed'          => 0,
            'chatsTotal'         => -1,
            'chatsRemaining'     => -1,
            'questionsUsed'      => 0,
            'questionsTotal'     => -1,
            'questionsRemaining' => -1,
            'historySize'        => -1,
            'questionSize'       => -1,
        ];
    }

    public function canAnnonAskQuestion(int|string $userId, int $currentChatMessageCount, int|string $chatbotId = 0): bool {
        return true;
    }

    public function getHistorySize(int|string $userId, int|string $chatbotId = 0): int {
        return -1;
    }

    public function updateTitle(int|string $userId, int|string $id, int|string $chatbotId = 0): bool {
        return true;
    }

    public function trash(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function remove(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function restore(int|string $userId, int|string $id, ...$args): bool {
        return true;
    }

    public function saveMessage(int|string $userId, int|string $id, int|string $chatbotId = 0): bool {
        return true;
    }
}
