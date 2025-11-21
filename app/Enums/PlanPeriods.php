<?php

namespace WPBulgaria\Chatbot\Enums;

defined( 'ABSPATH' ) || exit;

enum PlanPeriods: string
{
    case DAY = "day";
    case WEEK = "week";
    case MONTH = "month";
    case YEAR = "year";
    case LIFETIME = "lifetime";
}