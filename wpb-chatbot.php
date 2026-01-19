<?php

/**
Plugin Name: WPBulgaria Chatbot
Plugin URI: https://wpbulgaria.com
Description: A chatbot for WPBulgaria.
Author: Sashe Vuchkov
Version: 0.0.1
Author URI: https://wpbulgaria.com
Text Domain: wpbulgaria-chatbot
**/

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined( 'ABSPATH' ) || exit;

define ('_WPB_CHATBOT_UNLOCK_API', "unlock it all now");

define("WPB_CHATBOT_VERSION", "0.0.1");
define ('WPB_CHATBOT_URL', plugin_dir_url(__FILE__));
define ('WPB_CHATBOT_DIR',__DIR__);
define("WPB_CHATBOT_SEARCH_DELIMITER", "{!--!}");
define("WPB_CHATBOT_API_PREFIX", 'wpb-chatbot/v1');
define("WPB_CHATBOT_FILE_IN_USE_FIELD", "wpb_chatbot_file_in_use");

require_once(WPB_CHATBOT_DIR."/vendor/autoload.php");
require_once(WPB_CHATBOT_DIR.'/functions.php');
require_once(WPB_CHATBOT_DIR.'/post-types/chat.php');
require_once(WPB_CHATBOT_DIR.'/app/Api/Api.php');
require_once(WPB_CHATBOT_DIR.'/hooks/attachments.php');

use WPBulgaria\Chatbot\Models\SearchFileModel;

function wpbulgaria_chatbot_enqueue_styles() {
    wp_enqueue_style('wpbulgaria-chatbot-global', WPB_CHATBOT_URL.'/assets/global.css', array(), WPB_CHATBOT_VERSION, 'all');
}   

add_action('admin_enqueue_scripts', 'wpbulgaria_chatbot_enqueue_styles');

function wpbulgaria_chatbot_admin_include_app() {
   include_once(WPB_CHATBOT_DIR.'/assets/admin/template.php');
}

add_action("admin_menu", function() {
    add_menu_page(
        __( "Chatbot", 'wpbulgaria-chatbot' ),
        __( "Chatbot", 'wpbulgaria-chatbot' ),
        "manage_options",
        "wpbulgaria-chatbot",
        "wpbulgaria_chatbot_admin_include_app");
});


function wpbulgaria_chatbot_shortcode($atts = array(), $content = null) {
    $configs = ConfigsModel::view(true);
    $chatTheme = is_array($configs["chatTheme"]) ? json_encode($configs["chatTheme"], JSON_UNESCAPED_UNICODE) : "null";
    ob_start();
    $template_file = WPB_CHATBOT_DIR . '/assets/chat/template.php';
    if ( file_exists($template_file) ) {
        include $template_file;
    } else {
        echo esc_html__('Chatbot template not found.', 'wpbulgaria-chatbot');
    }
    return ob_get_clean();
}
add_shortcode('wpbulgaria_chatbot', 'wpbulgaria_chatbot_shortcode');



register_activation_hook( __FILE__, 'wpbulgaria_chatbot_install' );
function wpbulgaria_chatbot_install() {
    global $wpdb;
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    /*
    $collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;
    $sql = "";


    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql ); 
    */

    add_option( 'wpbulgaria_chatbot_db_version', 1);   
}