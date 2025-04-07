<?php

//wp_enqueue_script( 'experts-crm-init-js', '__INIT_JS__', false );

?>

<script>
    jQuery(document).ready(function($) {
        window.expertsCRMConfig = {
            root: "<?php echo esc_url_raw( rest_url() ); ?>",
            nonce: "<?php echo wp_create_nonce( 'wp_rest' ); ?>"
        };

        var el = $("<iframe></iframe>");
        el.attr("style", "width: 100%; border: 0;");
        el.attr("src", "/wp-content/plugins/experts-crm/assets/plugin.html" + window.location.hash);
        el.attr("id", "experts-crm-iframe")
        $("#experts-crm-iframe-container").append(el);

        el.on("load", function () {
            const iBody = el.contents().find(".content-area");
            if (iBody.length) {
                new ResizeObserver(function() {
                    var height = iBody.scrollHeight > document.body.offsetHeight ? iBody.scrollHeight : document.body.offsetHeight;
                    el.height(height + 50);
                }).observe(iBody[0]);
            }
        })
    })
</script>


<div id="experts-crm-iframe-container"></div>