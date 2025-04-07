<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum Phases: string {
    case STRANGER = "stranger";
    case LEAD = "lead";
    case OPPORTUNITY = "opportunity";
    case CLIENT = "client";
    case REPEAT_CLIENT = "repeat_client";
    case FRIEND = "friend";
    case PARTNER = "partner";
}
