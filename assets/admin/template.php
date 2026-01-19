<?php
defined('ABSPATH') || exit;
$plugin_url = plugin_dir_url(dirname(__DIR__));
?>

<div id="wp-chatbot-admin-container"></div>
<script type="module" src="<?php echo esc_url($plugin_url . "/assets/admin/assets/index-DyAZnEc3.js", ); ?>"></script>
<script>
    
    window.wpbChatbotConfig = {
            root: "<?php echo esc_url_raw( rest_url() ); ?>",
            nonce: "<?php echo wp_create_nonce( 'wp_rest' ); ?>"
        };

    jQuery(document).ready(function($) {
        $("#wp-chatbot-admin-container").on("keyup", function(event) {
            event.stopPropagation();
        })

        $("#wp-chatbot-admin-container").on("keydown", function(event) {
            event.stopPropagation();
        })


        /**
        let link = document.createElement("link");
        link.setAttribute("rel", "stylesheet");
        link.setAttribute("href", "/wp-content/plugins/wpb-chatbot/assets/admin__STYLE_LINK1__");

        */

        let bodyLink = document.createElement("link");
        bodyLink.setAttribute("rel", "stylesheet");
        bodyLink.setAttribute("href", '<?php echo esc_url($plugin_url . "/assets/admin/assets/index-B54ZwsF9.css"); ?>');
        document.body.prepend(bodyLink);

        /**

        let style = document.createElement("style");
        style.innerHTML = ` `;


        let host = document.getElementById("wp-chatbot-admin-container");
        host.shadowRoot.prepend(link);
        host.shadowRoot.prepend(style);
        */
    });
</script>