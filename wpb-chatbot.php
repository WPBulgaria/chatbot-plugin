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

defined( 'ABSPATH' ) || exit;

define ('_WPB_CHATBOT_UNLOCK_API', $_SERVER["HTTP_HOST"] === "wpstudio.local");

define("WPB_CHATBOT_VERSION", "0.0.1");
define ('WPB_CHATBOT_URL', plugin_dir_url(__FILE__));
define ('WPB_CHATBOT_DIR',__DIR__);
define("WPB_CHATBOT_SEARCH_DELIMITER", "{!--!}");
define("WPB_CHATBOT_API_PREFIX", 'wpb-chatbot/v1');

require_once(WPB_CHATBOT_DIR."/vendor/autoload.php");
require_once(WPB_CHATBOT_DIR.'/functions.php');
require_once(WPB_CHATBOT_DIR.'/app/Api/Api.php');


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