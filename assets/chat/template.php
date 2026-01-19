<?php
defined('ABSPATH') || exit;
$plugin_url = plugin_dir_url(dirname(__DIR__));
?>

<div id="wp-chatbot-chat-container"></div>
<script type="module" src="<?php echo esc_url($plugin_url . "/assets/chat/assets/index-BSIrv67b.js", ); ?>"></script>
<script>
    
    window.wpbChatbotConfig = {
            root: "<?php echo esc_url_raw( rest_url() ); ?>",
            nonce: "<?php echo wp_create_nonce( 'wp_rest' ); ?>",
            chatTheme: "<?php echo esc_js($chatTheme); ?>"
        };

    jQuery(document).ready(function($) {
        $("#wp-chatbot-chat-container").on("keyup", function(event) {
            event.stopPropagation();
        })

        $("#wp-chatbot-chat-container").on("keydown", function(event) {
            event.stopPropagation();
        })

        let bodyLink = document.createElement("link");
        bodyLink.setAttribute("rel", "stylesheet");
        bodyLink.setAttribute("href", '<?php echo esc_url($plugin_url . "/assets/chat/assets/index-DvhKTwQN.css"); ?>');
        document.body.prepend(bodyLink);
    });
</script>