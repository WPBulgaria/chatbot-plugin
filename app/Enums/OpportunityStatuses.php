<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum OpportunityStatuses: string
{
    case NEVER = "never";
    case IN_PROGRESS = "in_progress";
    case WON = "won";
    case LOST = "lost";
    case SUSPENDED = "suspended";
    case ABANDONED = "abandoned";
}