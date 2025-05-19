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
        el.attr("style", "width: 100%;  height: 98vh; border: 0;");
        el.attr("src", "/wp-content/plugins/experts-crm/assets/plugin.html" + window.location.hash);
        el.attr("id", "experts-crm-iframe")
        $("#experts-crm-iframe-container").append(el);
    })
</script>


<div id="experts-crm-iframe-container"></div>