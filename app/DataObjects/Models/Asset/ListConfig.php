<?php

namespace ExpertsCrm\DataObjects\Models\Asset;

use ExpertsCrm\Enums\SortOptions;

defined( 'ABSPATH' ) || exit;

class ListConfig {
    public array $types;
    public int $pointer;
    public string $query;
    public string $sort;

    function __construct(array $types = [], int $pointer = 0, string $query = '', ?string $sort) {
        $this->types = $types;
        $this->pointer = $pointer;
        $this->query = !empty($query) ? "*".$query."*" : "";
        $this->sort = $sort ?: SortOptions::DESC->value;
    }
}