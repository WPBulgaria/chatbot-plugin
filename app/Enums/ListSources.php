<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum ListSources: string
{
    case FACEBOOK = "facebook";
    case FACEBOOK_GROUP = "facebook_group";
    case LINKEDIN = "linkedin";
    case INSTAGRAM = "instagram";
    case TIKTOK = "tiktok";
    case GOOGLE_SEARCH = "google_search";
    case OTHER = "other";
    case CSV = "csv";
}