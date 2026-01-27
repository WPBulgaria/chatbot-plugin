<?php

namespace WPBulgaria\Chatbot\Models;

use function WPBulgaria\Chatbot\Functions\genId;

defined( 'ABSPATH' ) || exit;

class PlanModel {

    const OPTIONS_KEY = "wpb_chatbot_plans";
    const USER_PLAN_META_KEY = "wpb_chatbot_user_plan";

    protected PostModel $postModel;

    public function __construct(PostModel $postModel) {
        $this->postModel = $postModel;
    }

    public function list(int|string $chatbotId): array {
        $plans = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY) ?? [];
        if (empty($plans)) {
            return [];
        }
        
        return array_values(array_filter($plans, fn($plan) => empty($plan["removedAt"])));
    }

    /**
     * Get a plan by ID
     */
    public function get(int|string $chatbotId, string $id): ?array {
        if (empty($id)) {
            return null;
        }

        $plans = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY) ?? [];
        if (empty($plans)) {
            return null;
        }

        foreach ($plans as $plan) {
            if ($plan["id"] === $id && empty($plan["removedAt"])) {
                return $plan;
            }
        }

        return null;
    }

    public function getUserPlans(int|string $userId): array {
        if ($userId <= 0) {
            return [];
        }

        $planIds = get_user_meta($userId, self::USER_PLAN_META_KEY, true) ?? [];
        return $planIds;
    }

    /**
     * Get user's assigned plan ID from user meta
     */
    public function getUserPlanId(int|string $chatbotId, int|string $userId): ?string {
        if ($userId <= 0) {
            return null;
        }

        $planIds = get_user_meta($userId, self::USER_PLAN_META_KEY, true) ?? [];

        if (empty($planIds)) {
            return null;
        }

        foreach ($planIds as $planId) {
            if ($planId["chatbotId"] === $chatbotId) {
                return $planId["planId"];
            }
        }

        return null;
    }

    /**
     * Set user's plan ID in user meta
     */
    public function setUserPlan(int|string $chatbotId, int|string $userId, string $planId): bool {
        if ($userId <= 0) {
            return false;
        }   

        $planIds = get_user_meta($userId, self::USER_PLAN_META_KEY, true) ?? [];

        if (empty($planIds)) {
            $planIds = [];
        }

        $planIds[] = [
            "chatbotId" => $chatbotId,
            "planId" => $planId,
        ];

        return (bool) update_user_meta($userId, self::USER_PLAN_META_KEY, $planIds);
    }

    public function store(int|string $chatbotId, array $doc) {
        $plans = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY) ?: [];
        if (empty($doc)) {
            return [];
        }

        $isNew = empty($doc["id"]);
        $id = $isNew ? genId() : sanitize_text_field($doc["id"]);
        $doc["id"] = $id;
        $doc["modifiedAt"] = date(DATE_ATOM);
        if ($isNew) {
            $doc["createdAt"] = date(DATE_ATOM);
        }

        if (empty($id)) {
            throw new \Exception("Invalid data", 400);
        }

        if ($isNew) {
            $plans[] = $doc;
        } else {
            foreach ($plans as $key => $plan) {
                if ($plan["id"] === $id) {
                    $plans[$key] = $doc;
                    break;
                }
            }
        }

        $this->postModel->updateMeta($chatbotId, self::OPTIONS_KEY, $plans);
        return $id;
    }

    public function trash(int $chatbotId, string $id) {
        $plans = $this->postModel->getMeta($chatbotId, self::OPTIONS_KEY) ?? [];
        if (empty($plans)) {
            return [];
        }

        foreach ($plans as $key => $plan) { 
            if ($plan["id"] === $id) {
                $plans[$key]["removedAt"] = date(DATE_ATOM);
                break;
            }
        }

        $this->postModel->updateMeta($chatbotId, self::OPTIONS_KEY, $plans);
        return true;
    }

}
