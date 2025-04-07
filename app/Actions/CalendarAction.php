<?php

namespace ExpertsCrm\Actions;


use ExpertsCrm\Enums\ObjectTypes;
use ExpertsCrm\Models\CalendarModel;
use Ramsey\Uuid\Uuid;
use function ExpertsCrm\validateDate;

defined( 'ABSPATH' ) || exit;

class CalendarAction {

    static function list(\WP_REST_Request $request) {
        $start = date("Y-01-01 00:00:00");
        $end = date("Y-12-31 23:59:59");

        $data = $request->get_params();

        if (!empty($data["start"]) && validateDate($data["start"])) {
            $start = $data["start"];
        }

        if (!empty($data["end"]) && validateDate($data["end"])) {
            $end = $data["end"];
        }

        $rows = CalendarModel::list($start, $end);
        return new \WP_REST_Response($rows, 200);
    }

    static function listByObjectId(\WP_REST_Request $request) {
        $start = date("Y-01-01 00:00:00");
        $end = date("Y-12-31 23:59:59");

        $data = $request->get_params();

        if (!Uuid::isValid($data["id"])) {
            return new \WP_REST_Response(["message" => "invalid data"], 400);
        }

        if (!empty($data["start"]) && validateDate($data["start"])) {
            $start = $data["start"];
        }

        if (!empty($data["end"]) && validateDate($data["end"])) {
            $end = $data["end"];
        }

        $rows = CalendarModel::listByObjectId($start, $end, $data["id"]);
        return new \WP_REST_Response($rows, 200);
    }

    static function listByObjectType(\WP_REST_Request $request) {
        $start = date("Y-01-01 00:00:00");
        $end = date("Y-12-31 23:59:59");

        $data = $request->get_params();

        if (!!ObjectTypes::tryFrom($data["type"])) {
            return new \WP_REST_Response(["message" => "invalid data"], 400);
        }

        if (!empty($data["start"]) && validateDate($data["start"])) {
            $start = $data["start"];
        }

        if (!empty($data["end"]) && validateDate($data["end"])) {
            $end = $data["end"];
        }

        $rows = CalendarModel::listByObjectType($start, $end, $data["type"]);
        return new \WP_REST_Response($rows, 200);
    }
}