<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum AssetTypes: string
{
    case TASK = 'task';
    case CONTACT = 'contact';
    case ADDRESS = 'address';
    case LOG = 'log';
    case TOUCH = 'touch';
    case COMPANY = "company";
    case PRODUCT = "product";
    case QUOTE = "quote";
    case INVOICE = "invoice";
    case PROJECT = "project";
    case CAMPAIGN = "campaign";
    case OPPORTUNITY = "opportunity";
    case HABIT = "habit";
    case GOAL = "goal";
    case NOTE = "note";
}