<?php

use WPBulgaria\Chatbot\Models\SearchFileModel;

function wpdocs_delete_attachment( $post_id ) {
	// Do something before attachment deleted

    $attachment = get_post($post_id);
    $inUse = get_post_meta($post_id, WPB_CHATBOT_FILE_IN_USE_FIELD, true);
    if (empty($inUse) || !is_array($inUse) || !in_array($attachment->post_parent, $inUse)) {
        return;
    }

    try {
        wpb_chatbot_app(SearchFileModel::class)->remove($attachment->guid, $attachment->post_parent); 
    } catch (\Exception $e) {
        error_log($e->getMessage());
        return;
    }
    return;
}
add_action( 'delete_attachment', 'wpdocs_delete_attachment' );