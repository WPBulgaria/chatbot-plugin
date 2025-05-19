<?php

/**
Plugin Name: Experts CRM
Plugin URI: https://crm.experts.pub
Description: A superb CRM that helps you broke the limits of your freelance business.
Author: Sashe Vuchkov
Version: 1.1.0
Author URI: https://buhalbu.com/hire
Text Domain: experts-crm
**/

defined( 'ABSPATH' ) || exit;

define ('_EXPERTS_CRM_UNLOCK_API', $_SERVER["HTTP_HOST"] === "wpstudio.local");

define("EXPERTS_CRM_VERSION", "1.0.0");
define ('EXPERTS_CRM_URL', plugin_dir_url(__FILE__));
define ('EXPERTS_CRM_DIR',__DIR__);
define("EXPERTS_CRM_SEARCH_DELIMITER", "{!--!}");

require_once(EXPERTS_CRM_DIR."/vendor/autoload.php");
require_once(EXPERTS_CRM_DIR.'/functions.php');
require_once(EXPERTS_CRM_DIR.'/app/Api/Api.php');

function experts_crm_include_app() {
   include_once(EXPERTS_CRM_DIR.'/assets/template.php');
}

add_action("admin_menu", function() {
    add_menu_page(
        __( "Experts CRM", 'experts-crm' ),
        __( "Experts CRM", 'experts-crm' ),
        "manage_options",
        "experts-crm",
        "experts_crm_include_app");
});

add_action('admin_head', 'experts_crm_head');
function experts_crm_head() {
    if(!empty($_GET["page"]) && $_GET["page"] == "experts-crm") {
        include(EXPERTS_CRM_DIR.'/assets/head.php');
    }
}


register_activation_hook( __FILE__, 'experts_crm_install' );
function experts_crm_install() {
    global $wpdb;
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    $collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;
    $sql = [
            "
                 CREATE TABLE `{$prefix}experts_crm_actions` (
                      `id` varchar(100) NOT NULL,
                      `doc` longtext DEFAULT NULL,
                      `created_at` datetime NOT NULL,
                      `modified_at` datetime NOT NULL,
                      `removed_at` datetime DEFAULT NULL,
                      `parent_id` varchar(100) DEFAULT NULL,
                      `parent_type` varchar(100) DEFAULT NULL,
                      `type` varchar(100) DEFAULT NULL,
                      `starts_at` datetime DEFAULT NULL,
                      `finishes_at` datetime DEFAULT NULL,
                      `completed_at` datetime DEFAULT NULL,
                      `repeat_until` datetime DEFAULT NULL,
                      `search_text` text DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      FULLTEXT KEY `{$prefix}experts_crm_actions_search_text_IDX` (`search_text`)
                    ) ENGINE=InnoDB $collate;
            ",

            "
                CREATE TABLE `{$prefix}experts_crm_assets` (
                  `id` varchar(100) NOT NULL,
                  `doc` longtext DEFAULT NULL,
                  `created_at` datetime NOT NULL,
                  `modified_at` datetime NOT NULL,
                  `removed_at` datetime DEFAULT NULL,
                  `parent_id` varchar(100) DEFAULT NULL,
                  `parent_type` varchar(100) DEFAULT NULL,
                  `type` varchar(100) DEFAULT NULL,
                  `search_text` longtext DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `{$prefix}experts_crm_assets_created_at_IDX` (`created_at`) USING BTREE,
                  FULLTEXT KEY `{$prefix}experts_crm_assets_search_text_IDX` (`search_text`)
                ) ENGINE=InnoDB $collate;
            ",
            "
                CREATE TABLE `{$prefix}experts_crm_people` (
                  `id` varchar(100) NOT NULL,
                  `doc` longtext DEFAULT NULL,
                  `created_at` datetime NOT NULL,
                  `modified_at` datetime NOT NULL,
                  `removed_at` datetime DEFAULT NULL,
                  `list_id` varchar(100) DEFAULT NULL,
                  `company_id` varchar(100) DEFAULT NULL,
                  `search_text` text DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `{$prefix}experts_crm_people_created_at_IDX` (`created_at`) USING BTREE,
                  KEY `{$prefix}experts_crm_people_list_id_IDX` (`list_id`,`removed_at`) USING BTREE,
                  FULLTEXT KEY `{$prefix}experts_crm_people_search_text_IDX` (`search_text`)
                ) ENGINE=InnoDB $collate;
            ",
            "
               CREATE TABLE `{$prefix}experts_crm_lists` (
                  `id` varchar(100) NOT NULL,
                  `doc` longtext DEFAULT NULL,
                  `removed_at` datetime DEFAULT NULL,
                  `created_at` datetime NOT NULL,
                  `modified_at` datetime NOT NULL,
                  `search_text` text DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  FULLTEXT KEY `{$prefix}experts_crm_lists_search_text_IDX` (`search_text`)
                ) ENGINE=InnoDB $collate;
            "
    ];


    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    add_option( 'experts_crm_db_version', 1);
}