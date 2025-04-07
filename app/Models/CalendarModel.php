<?php

namespace ExpertsCrm\Models;
use ExpertsCrm\Transformers\ActionTransformers\ApplicatorActionTransformer;

defined( 'ABSPATH' ) || exit;

class CalendarModel extends BaseModel {

    static function getTable() {
        global $wpdb;
        return $wpdb->prefix."experts_crm_actions";
    }

    static function list(string $start_date, string $end_date)
    {
        global $wpdb;

        $actionsTable = ActionModel::getTable();
        $assetsTable = AssetModel::getTable();

        //One-Time Task: 01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt

        //Repeat Task:   01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt || current_date < completedAt
        //if repeatUntil then no finishesAt
        //if completedAt then it overrides repeatUntil

        //Task start_date <= startsAt <= end_date  || end_date <= finishesAt
        //Repeat Task:  end_date <= repeatUntil || end_date <= compleatedAt

        //Project === One-Time Task === Campaign

        $query = $wpdb->prepare(
            "SELECT actions.*,
                    (SELECT doc FROM $assetsTable AS assets WHERE assets.id = actions.parent_id) AS `object`
                   FROM $actionsTable AS actions 
                   WHERE 
                       removed_at IS NULL
                       AND ((actions.starts_at > %s AND actions.starts_at < %s AND actions.repeat_until IS NULL) 
                      OR (actions.finishes_at < %s AND actions.repeat_until IS NULL)
                      OR (actions.repeat_until < %s AND actions.completed_at IS NULL)
                      OR (actions.repeat_until IS NOT NULL AND actions.completed_at < %s))",
            $start_date,
            $end_date,
            $end_date,
            $end_date,
            $end_date
        );

        $results = $wpdb->get_results($query,         ARRAY_A);
        return ApplicatorActionTransformer::apply($results);
    }

    static function listByObjectId(string $start_date, string $end_date, string $object_id)
    {
        global $wpdb;

        $actionsTable = ActionModel::getTable();
        $assetsTable = AssetModel::getTable();

        //One-Time Task: 01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt

        //Repeat Task:   01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt || current_date < completedAt
        //if repeatUntil then no finishesAt
        //if completedAt then it overrides repeatUntil

        //Task start_date <= startsAt <= end_date  || end_date <= finishesAt
        //Repeat Task:  end_date <= repeatUntil || end_date <= compleatedAt

        //Project === One-Time Task === Campaign

        $query = $wpdb->prepare(
            "SELECT actions.*,
                    (SELECT doc FROM $assetsTable AS assets WHERE assets.id = actions.parent_id) AS `object`
                   FROM $actionsTable AS actions 
                   WHERE parent_id = %s
                   AND removed_at IS NULL
                      AND ((actions.starts_at > %s AND actions.starts_at < %s AND actions.repeat_until IS NULL) 
                      OR (actions.finishes_at < %s AND actions.repeat_until IS NULL)
                      OR (actions.repeat_until < %s AND actions.completed_at IS NULL)
                      OR (actions.repeat_until IS NOT NULL AND actions.completed_at < %s))",
            $object_id,
            $start_date,
            $end_date,
            $end_date,
            $end_date,
            $end_date
        );

        $results = $wpdb->get_results($query,         ARRAY_A);
        return ApplicatorActionTransformer::apply($results);
    }

    static function listByObjectType(string $start_date, string $end_date, string $object_type)
    {
        global $wpdb;

        $actionsTable = ActionModel::getTable();
        $assetsTable = AssetModel::getTable();

        //One-Time Task: 01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt

        //Repeat Task:   01.04.2025 00:00:00 < startsAt || 30.04.2025 23:59:59 < finishesAt || current_date < completedAt
        //if repeatUntil then no finishesAt
        //if completedAt then it overrides repeatUntil

        //Task start_date <= startsAt <= end_date  || end_date <= finishesAt
        //Repeat Task:  end_date <= repeatUntil || end_date <= compleatedAt

        //Project === One-Time Task === Campaign

        $query = $wpdb->prepare(
            "SELECT actions.*,
                    (SELECT doc FROM $assetsTable AS assets WHERE assets.id = actions.parent_id) AS `object`
                   FROM $actionsTable AS actions 
                   WHERE parent_type = %s
                      AND removed_at IS NULL
                      AND ((actions.starts_at > %s AND actions.starts_at < %s AND actions.repeat_until IS NULL) 
                      OR (actions.finishes_at < %s AND actions.repeat_until IS NULL)
                      OR (actions.repeat_until < %s AND actions.completed_at IS NULL)
                      OR (actions.repeat_until IS NOT NULL AND actions.completed_at < %s))",
            $object_type,
            $start_date,
            $end_date,
            $end_date,
            $end_date,
            $end_date
        );

        $results = $wpdb->get_results($query,         ARRAY_A);
        return ApplicatorActionTransformer::apply($results);
    }
}