<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum Statuses: string {
    case NONE = "none";
    case COMPLETED = "completed";
    case CANCELED = "canceled";
    case IN_PROGRESS = "in_progress";
    case ON_HOLD = "on_hold";
    case NOT_STARTED = "not_started";
}

