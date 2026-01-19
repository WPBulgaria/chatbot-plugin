<?php

namespace WPBulgaria\Chatbot\Models;

defined('ABSPATH') || exit;

class OptionModel {

    public function get(string $key, mixed $default = false): mixed {
        return get_option($key, $default);
    }

    public function update(string $key, mixed $value, string $autoload = 'no'): bool {
        return update_option($key, $value, $autoload);
    }

    public function delete(string $key): bool {
        return delete_option($key);
    }

    public function add(string $key, mixed $value, string $autoload = 'no'): bool {
        return add_option($key, $value, '', $autoload);
    }
}