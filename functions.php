<?php
namespace WPBulgaria\Chatbot\Functions;

use Ramsey\Uuid\Uuid;
defined( 'ABSPATH' ) || exit;

function authorize() {
    return current_user_can( 'manage_options' );
}


function genId() {
    return Uuid::uuid4()->toString();
}
    
function validateDate($date, $format = DATE_ATOM)
{
    try {
        return substr($date, 0, 19) === substr(date(DATE_ATOM, strtotime($date)), 0, 19);
    } catch (\Exception $e) {
        return false;
    }
}

function edit_upload_types($existing_mimes = array()) {

    $existing_mimes['json'] = 'application/json';
    $existing_mimes['pdf'] = 'application/pdf';
    $existing_mimes['txt'] = 'text/plain';
    $existing_mimes['csv'] = 'text/csv';
    $existing_mimes['tsv'] = 'text/tab-separated-values';
    $existing_mimes['xml'] = 'application/xml';
 
    return $existing_mimes;
}
add_filter('upload_mimes', 'WPBulgaria\Chatbot\Functions\edit_upload_types');