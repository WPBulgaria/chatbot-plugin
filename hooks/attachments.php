<?php

use WPBulgaria\Chatbot\Models\SearchFileModel;
use function wpb_chatbot_app;
function wpdocs_delete_attachment( $post_id ) {
	// Do something before attachment deleted

    $attachment = get_post($post_id);
    $inUse = get_post_meta($post_id, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
    if ($inUse !== '1') {
        return;
    }

    try {
        wpb_chatbot_app(SearchFileModel::class)->remove($attachment->guid); 
    } catch (\Exception $e) {
        error_log($e->getMessage());
        return;
    }
    return;
}
add_action( 'delete_attachment', 'wpdocs_delete_attachment' );