<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum Priority: string {
    case NONE = "none";
    case LOW = "low";
    case MEDIUM = "medium";
    case HIGH = "high";
}