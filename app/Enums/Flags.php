<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum Flags: string {
    case NONE = 'none';
    case AVOID = "avoid";
    case LOW_PRIORITY = "low_priority";
    case MEDIUM_PRIORITY = "medium_priority";
    case HIGH_PRIORITY = "high_priority";
}