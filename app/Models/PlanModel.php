<?php

namespace WPBulgaria\Chatbot\Models;

use function WPBulgaria\Chatbot\Functions\genId;

defined( 'ABSPATH' ) || exit;

class PlanModel {

    const OPTIONS_KEY = "wpb_chatbot_plans";

    protected OptionModel $optionModel;

    public function __construct(OptionModel $optionModel) {
        $this->optionModel = $optionModel;
    }

    public function list() {
        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
        if (empty($plans)) {
            return [];
        }
        
        return array_values(array_filter($plans, fn($plan) => empty($plan["removedAt"])));
    }

    public function store(array $doc) {
        $plans = $this->optionModel->get(self::OPTIONS_KEY, []);
        if (empty($plans)) {
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
