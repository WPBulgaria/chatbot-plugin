<?php

namespace WPBulgaria\Chatbot\Models;

use function WPBulgaria\Chatbot\Functions\genId;

defined( 'ABSPATH' ) || exit;

class PlanModel {

    const OPTIONS_KEY = "wpb_chatbot_plans";


    public static function list() {
        $plans = get_option(self::OPTIONS_KEY, []);
        if (empty($plans)) {
            return [];
        }
        
        return array_values(array_filter($plans, fn($plan) => empty($plan["removedAt"])));
    }

    public static function store(array $doc) {
        $plans = get_option(self::OPTIONS_KEY, []);
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

        update_option(self::OPTIONS_KEY, $plans);
        return $id;
    }

    public static function trash(string $id) {
        $plans = get_option(self::OPTIONS_KEY, []);
        if (empty($plans)) {
            return [];
        }

        foreach ($plans as $key => $plan) {
            if ($plan["id"] === $id) {
                $plans[$key]["removedAt"] = date(DATE_ATOM);
                break;
            }
        }

        update_option(self::OPTIONS_KEY, $plans);
        return true;
    }

}
