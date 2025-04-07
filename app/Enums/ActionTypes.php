<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum ActionTypes: string
{
    case NONE = "none";
    case ONE_TIME = "one_time";
    case SCHEDULED = "scheduled";
    case COMPLETED = "completed";
    case RESCHEDULED = "rescheduled";
    case CANCELED = "canceled";
    case ARCHIVED = "archived";
    case META = "meta";
}