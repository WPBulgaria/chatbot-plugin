<?php

namespace ExpertsCrm\Transformers\ActionTransformers;;

use ExpertsCrm\Enums\SchedulePeriods;
use ExpertsCrm\Transformers\ITransformer;
use const ExpertsCrm\SchedulePeriodsMap;

defined( 'ABSPATH' ) || exit;

class DefaultActionTransformer implements ITransformer {

    static function apply($action)
    {
        $rrule = [];
        if ($action["period"] !== SchedulePeriods::NEVER->value && !!SchedulePeriods::tryFrom($action["period"])) {
            $rrule = [
                "freq" => SchedulePeriodsMap[$action["period"]]["freq"],
                "interval" => SchedulePeriodsMap[$action["period"]]["interval"],
                "dtstart" => $action["startsAt"],
                "until" => $action["repeatUntil"],
            ];
        }

        $event = [
            "id" => $action["_id"],
            "item" => $action,
            "title" => isset($action["object"]["subtype"]) && $action["object"]["subtype"] !== "none" ? $action["object"]["subtype"] : $action["object"]["name"],
            "start" => $action["startsAt"],
            "end" => $action["finishesAt"],
            "color" =>  $action["object"]["color"] ?? "#000000",
            "allDay" => !!$action["allDay"],
        ];

        if (!empty($rrule)) {
            $event["rrule"] = $rrule;
        }

        return $event;
    }
}