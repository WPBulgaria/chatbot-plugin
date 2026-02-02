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

function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

function user_rate_limit_exceeded(): bool {
    $user_id = get_current_user_id();
    $identifier = $user_id ?: get_ip_address() ?? 'unknown';
    $rate_key = 'wpb_chat_rate_' . md5($identifier);

    $rate_count = get_transient($rate_key) ?: 0;

    if ($rate_count >= WPB_CHATBOT_RATE_LIMIT) { // WPB_CHATBOT_RATE_LIMIT requests per minute
        return true;
    }

    set_transient($rate_key, $rate_count + 1, 60);
    return false;
}

function mb_str_word_count($string) {
    if (empty($string)) {
        return 0;
    }

    // Match any sequence of Unicode letters, numbers, or apostrophes
    // Removed the '^' to match words instead of separators
    preg_match_all('/[\p{L}\p{N}\']+/u', $string, $matches);
    
    return count($matches[0]);
}