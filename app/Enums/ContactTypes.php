<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum ContactTypes:string
{
    case FACEBOOK = "facebook";
    case INSTAGRAM = "instagram";
    case TIKTOK = "tiktok";
    case LINKEDIN = "linkedin";
    case PHONE = "phone";
    case EMAIL = "email";
    case WEBSITE = "website";
}