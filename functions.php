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