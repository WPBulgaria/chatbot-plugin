<?php

/**
Plugin Name: WPBulgaria Chatbot
Plugin URI: https://wpbulgaria.com
Description: A chatbot for WPBulgaria.
Author: Sashe Vuchkov
Version: 0.0.2
Author URI: https://wpbulgaria.com
Text Domain: wpbulgaria-chatbot

Requires at least: 6.9
Tested up to: 6.9
Requires PHP: 8.4

**/

defined('ABSPATH') || exit;

// Constants

define('_WPB_CHATBOT_DEBUG', false);
define('_WPB_CHATBOT_UNLOCK_API', false);
define('WPB_CHATBOT_VERSION', '0.0.3');
define('WPB_CHATBOT_URL', plugin_dir_url(__FILE__));
define('WPB_CHATBOT_DIR', __DIR__);
define('WPB_CHATBOT_SEARCH_DELIMITER', '{!--!}');
define('WPB_CHATBOT_API_PREFIX', 'wpb-chatbot/v1');
define('WPB_CHATBOT_FILE_IN_USE_FIELD', 'wpb_chatbot_file_in_use');

// Autoloader
require_once WPB_CHATBOT_DIR . '/vendor/autoload.php';

// Helper functions
require_once WPB_CHATBOT_DIR . '/app/helpers.php';

// Legacy includes (to be refactored)
require_once WPB_CHATBOT_DIR . '/functions.php';
require_once WPB_CHATBOT_DIR . '/post-types/chat.php';
require_once WPB_CHATBOT_DIR . '/post-types/chatbot.php';
require_once WPB_CHATBOT_DIR . '/app/Api/Api.php';
require_once WPB_CHATBOT_DIR . '/hooks/attachments.php';
require_once WPB_CHATBOT_DIR . '/hooks/users.php';
require_once WPB_CHATBOT_DIR . '/hooks/shortcode.php';

use WPBulgaria\Chatbot\Application;

/**
 * Bootstrap the application
 */
function wpbulgaria_chatbot_bootstrap(): Application {
    return Application::getInstance()->boot();
}

// Boot the application on plugins_loaded
add_action('plugins_loaded', 'wpbulgaria_chatbot_bootstrap', 5);

/**
 * Enqueue admin styles
 */
function wpbulgaria_chatbot_enqueue_styles(): void {
    wp_enqueue_style(
        'wpbulgaria-chatbot-global',
        WPB_CHATBOT_URL . '/assets/global.css',
        [],
        WPB_CHATBOT_VERSION,
        'all'
    );
}
add_action('admin_enqueue_scripts', 'wpbulgaria_chatbot_enqueue_styles');

/**
 * Include admin app template
 */
function wpbulgaria_chatbot_admin_include_app(): void {
    include_once WPB_CHATBOT_DIR . '/assets/admin/template.php';
}

/**
 * Register admin menu
 */
add_action('admin_menu', function (): void {
    add_menu_page(
        __('Chatbot', 'wpbulgaria-chatbot'),
        __('Chatbot', 'wpbulgaria-chatbot'),
        'manage_options',
        'wpbulgaria-chatbot',
        'wpbulgaria_chatbot_admin_include_app'
    );
});


/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, 'wpbulgaria_chatbot_install');
function wpbulgaria_chatbot_install(): void {
    require_once WPB_CHATBOT_DIR . '/post-types/chat.php';
    require_once WPB_CHATBOT_DIR . '/post-types/chatbot.php';
    
    flush_rewrite_rules();
    add_option('wpbulgaria_chatbot_db_version', 1);
}
