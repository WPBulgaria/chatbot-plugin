<?php
namespace ExpertsCrm;

use ExpertsCrm\Enums\SchedulePeriods;
use Ramsey\Uuid\Uuid;

defined( 'ABSPATH' ) || exit;


function createSearchable(array $fields, array $doc) {
    $txt = "";
    foreach ($fields as $field) {
        $txt .= isset($doc[$field]) ? json_encode($doc[$field]).EXPERTS_CRM_SEARCH_DELIMITER : "";
    }

    return $txt;
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


const SchedulePeriodsMap = [
    "never" =>[
        "freq" => "never",
        "interval" => 0,
    ],
    "daily" => [
        "freq" => "daily",
        "interval" => 1,
    ],

    "twice_weekly" => [
        "freq" => "daily",
        "interval" => 4,
    ],

    "weekly" => [
        "freq" => "weekly",
        "interval" => 1,
    ],



    "twice_monthly" => [
        "freq" => "weekly",
        "interval" => 2,
    ],

    "monthly" => [
        "freq" => "monthly",
        "interval" => 1,
    ],

    "two_monthly" => [
        "freq" => "monthly",
        "interval" => 2,
    ],

    "three_monthly" => [
        "freq" => "monthly",
        "interval" => 3,
    ],

    "yearly" => [
        "freq" => "yearly",
        "interval" => 1,
    ],
];