<?php

namespace ExpertsCrm\DataObjects\Models\List;

use ExpertsCrm\Enums\SortOptions;

defined( 'ABSPATH' ) || exit;

class ListConfig {
    public int $pointer;
    public string $query;
    public string $sort;
    public bool $expanded;

    function __construct(int $pointer = 0, string $query = '', bool $expanded = false, ?string $sort) {
        $this->pointer = $pointer;
        $this->query = !empty($query) ? "*".$query."*" : "";
        $this->expanded = $expanded;
        $this->sort = $sort ?: SortOptions::DESC->value;
    }
}