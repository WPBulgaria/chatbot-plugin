<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum SchedulePeriods: string {
    case NEVER = "never";
    case DAILY = "daily";
    case TWICE_WEEKLY = "twice_weekly";
    case WEEKLY = "weekly";
    case TWICE_MONTHLY = "twice_monthly";
    case MONTHLY = "monthly";
    case TWO_MONTHLY = "two_monthly";
    case THREE_MONTHLY = "three_monthly";
    case SIX_MONTHLY = "six_monthly";
    case YEARLY = "yearly";
}