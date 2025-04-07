<?php

namespace ExpertsCrm\Transformers\ActionTransformers;;

use ExpertsCrm\Enums\SchedulePeriods;
use ExpertsCrm\Transformers\ITransformer;
use const ExpertsCrm\SchedulePeriodsMap;

defined( 'ABSPATH' ) || exit;

class TaskActionTransformer implements ITransformer {

    static function apply($action)
    {
        $events = [];
        $dueAt = $action["finishesAt"] ?? $action["startsAt"];
        $action["finishesAt"] = $action["startsAt"];
        $events[] = DefaultActionTransformer::apply($action);
        if ($dueAt !== $action["startsAt"]) {
            $action["startsAt"] = $dueAt;
            $action["finishesAt"] = $dueAt;
            $events[] = DefaultActionTransformer::apply($action);
        }

        return $events;
    }
}