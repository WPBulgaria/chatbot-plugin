<?php

namespace WPBulgaria\Chatbot\Models;

use function WPBulgaria\Chatbot\Functions\genId;

defined( 'ABSPATH' ) || exit;

class PlanModel {

    const OPTIONS_KEY = "wpb_chatbot_plans";
    const USER_PLAN_META_KEY = "wpb_chatbot_user_plan";

    protected OptionModel $optionModel;

    public function __construct(OptionModel $optionModel) {
        $this->optionModel = $optionModel;
    }

    public function list(): array {
        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
        if (empty($plans)) {
            return [];
        }
        
        return array_values(array_filter($plans, fn($plan) => empty($plan["removedAt"])));
    }

    /**
     * Get a plan by ID
     */
    public function get(string $id): ?array {
        if (empty($id)) {
            return null;
        }

        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
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

    /**
     * Get user's assigned plan ID from user meta
     */
    public function getUserPlanId(int $userId): ?string {
        if ($userId <= 0) {
            return null;
        }

        $planId = get_user_meta($userId, self::USER_PLAN_META_KEY, true);
        return !empty($planId) ? $planId : null;
    }

    /**
     * Set user's plan ID in user meta
     */
    public function setUserPlan(int $userId, string $planId): bool {
        if ($userId <= 0) {
            return false;
        }

        return (bool) update_user_meta($userId, self::USER_PLAN_META_KEY, $planId);
    }

    public function store(array $doc) {
        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
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

        $this->optionModel->update(self::OPTIONS_KEY, $plans);
        return $id;
    }

    public function trash(string $id) {
        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
        if (empty($plans)) {
            return [];
        }

        foreach ($plans as $key => $plan) {
            if ($plan["id"] === $id) {
                $plans[$key]["removedAt"] = date(DATE_ATOM);
                break;
            }
        }

        $this->optionModel->update(self::OPTIONS_KEY, $plans);
        return true;
    }

}
