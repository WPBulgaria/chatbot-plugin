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

    public function list(int $userId = 0): bool {
        return true;
    }

    public function get(int|string $id): bool {
        return true;
    }

    public function store(): bool {
        return true;
    }

    public function chat(int|string|null $id = null): bool {
        return true;
    }

    public function stream(int|string|null $id = null): bool {
        return true;
    }

    public function validateQuestionSize(string $message): bool {
        return true;
    }

    public function getUsageSummary(): array {
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

    public function canAnnonAskQuestion(int $currentChatMessageCount): bool {
        return true;
    }

    public function getHistorySize(): int {
        return -1;
    }

    public function updateTitle(int|string $id): bool {
        return true;
    }

    public function trash(int|string $id): bool {
        return true;
    }

    public function remove(int|string $id): bool {
        return true;
    }

    public function restore(int|string $id): bool {
        return true;
    }
}
