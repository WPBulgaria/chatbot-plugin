<?php

namespace WPBulgaria\Chatbot\Models;

defined( 'ABSPATH' ) || exit;

class ConfigsModel {

    const OPTIONS_KEY = "wpb_chatbot_configs";  
    
    const DEFAULT_CONFIGS = [
        "api_key" => "",
        "totalChats" => 100,
        "totalQuestions" => 100,
        "adminsOnly" => true,
        "publicPlan" => "",
        "defaultPlan" => "",
        "createdAt" => "",
        "modifiedAt" => "",
    ];

    public static function store(array $doc) {
        $configs = get_option(self::OPTIONS_KEY, []);
        if (empty($configs)) {
            $configs = self::DEFAULT_CONFIGS;
            $configs["modifiedAt"] = date(DATE_ATOM);
        } else {
            $configs["modifiedAt"] = date(DATE_ATOM);
        }

        $newDoc = array_merge($configs, $doc);
        update_option(self::OPTIONS_KEY, $newDoc);
        return true;
    }

    public static function view() {
        $configs = get_option(self::OPTIONS_KEY, []);
        if (empty($configs)) {
            return self::DEFAULT_CONFIGS;
        }

        return $configs;
    }

}
