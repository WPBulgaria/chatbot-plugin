<?php defined('ABSPATH') || exit; ?>

<div id="wp-chatbot-admin-container"></div>
<script type="module" src="/wp-content/plugins/wpb-chatbot/assets/admin/assets/index-K0gKOwzw.js"></script>
<script>
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
        bodyLink.setAttribute("href", "/wp-content/plugins/wpb-chatbot/assets/admin/assets/index-DA3Sk57J.css");
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