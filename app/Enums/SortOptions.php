<?php

namespace ExpertsCrm\Enums;

defined( 'ABSPATH' ) || exit;

enum SortOptions: string
{
    case ASC = "asc";
    case DESC = "desc";
}