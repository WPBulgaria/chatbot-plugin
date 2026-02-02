<?php

use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

$wpb_chatbot_shortcode_used = false;

/**
 * Chatbot shortcode
 * 
 * @param array $atts Shortcode attributes
 * @param string|null $content Shortcode content
 * @return string HTML output
 */
function wpbulgaria_chatbot_shortcode(array $atts = [], ?string $content = null): string {
    global $wpb_chatbot_shortcode_used;
    $wpb_chatbot_shortcode_used = true;
    
    $history = "on";
    if (!empty($atts["history"]) && $atts["history"] === "off") {
        $history = "off";
    }

    if (empty($atts["id"])) {
        return '<div class="wpb-chatbot-error">Chatbot ID is required</div>';
    }

    $chatTheme = null;
    try {
        $configs = wpb_chatbot_resolve(ConfigsModel::class)->view($atts["id"], true);
        if (!empty($configs["chatTheme"])) {
            $chatTheme = wp_json_encode($configs["chatTheme"], JSON_UNESCAPED_UNICODE);
        }
    } catch (\Exception $e) {
        return '<div class="wpb-chatbot-error">Failed to load chatbot config</div>';
    }

    return '<div id="wp-chatbot-chat-container" data-history="' . esc_attr($history) . '" data-chatbot="' . esc_attr($atts["id"]) . '" data-theme="' . esc_attr($chatTheme) . '"></div>';
}
add_shortcode('wpbulgaria_chatbot', 'wpbulgaria_chatbot_shortcode');

/**
 * Enqueue chatbot assets conditionally in footer
 */
function wpb_chatbot_enqueue_shortcode_assets(): void {
    global $wpb_chatbot_shortcode_used;
    
    if (!$wpb_chatbot_shortcode_used) {
        return;
    }
    
    $assets = include WPB_CHATBOT_DIR . '/assets/chat/assets.php';
    
    if (empty($assets['css']) || empty($assets['js'])) {
        error_log('WPB Chatbot: Asset URLs not found');
        return;
    }
    // Enqueue the JavaScript module
    wp_enqueue_script(
        'wpb-chatbot-chat',
        WPB_CHATBOT_URL . 'assets/chat' . $assets['js'],
        ['jquery'],
        WPB_CHATBOT_VERSION,
        true
    );

    try {
        // Pass config to JavaScript
        wp_localize_script('wpb-chatbot-chat', 'wpbChatbotConfig', [
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
            'cssUrl' => WPB_CHATBOT_URL . 'assets/chat' . $assets['css'] ,
        ]);
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to load shortcode config - ' . $e->getMessage());
    }
}
add_action('wp_print_footer_scripts', 'wpb_chatbot_enqueue_shortcode_assets', 1);

/**
 * Add module type attribute to chatbot script
 * 
 * @param string $tag Script tag HTML
 * @param string $handle Script handle
 * @return string Modified script tag
 */
function wpb_chatbot_add_module_type(string $tag, string $handle): string {
    if ($handle === 'wpb-chatbot-chat') {
        $tag = str_replace('<script ', '<script type="module" ', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'wpb_chatbot_add_module_type', 10, 2);
