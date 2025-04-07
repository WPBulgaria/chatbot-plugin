<?php

namespace ExpertsCrm\Transformers\ActionTransformers;;

use ExpertsCrm\Enums\ActionTypes;
use ExpertsCrm\Enums\ObjectTypes;
use ExpertsCrm\Enums\SchedulePeriods;
use ExpertsCrm\Transformers\ITransformer;
use const ExpertsCrm\SchedulePeriodsMap;

defined( 'ABSPATH' ) || exit;

class ApplicatorActionTransformer implements ITransformer {

    static function apply($actionRows)
    {
        if (!is_array($actionRows)) {
            throw new \Exception("actionRows must be an array");
        }

        $events = [];
        foreach ($actionRows as $row) {
            if (!is_array($row)) {
                throw new \Exception("each row must be an array");
            }


            $doc = json_decode($row['doc'], true);

            if (empty($doc)) {
                throw new \Exception("each row action doc must be a valid JSON object");
            }

            $object = json_decode($row['object'], true);
            $doc["object"] = $object;

            if ($doc['objectType'] === ObjectTypes::TASK->value) {
                $events = array_merge($events, TaskActionTransformer::apply($doc));
            } else {
                $events[] = DefaultActionTransformer::apply($doc);
            }
        }
        return $events;
    }
}