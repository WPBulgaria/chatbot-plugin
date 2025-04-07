<?php

namespace ExpertsCrm\DataObjects\Transactions\Company;

use ExpertsCrm\Enums\SortOptions;

defined( 'ABSPATH' ) || exit;

class ListConfig {
    public int $pointer;
    public string $query;
    public string $sort;

    function __construct(int $pointer = 0, string $query = '', ?string $sort) {
        $this->pointer = $pointer;
        $this->query = $query;
        $this->sort = $sort ?: SortOptions::DESC->value;
    }
}