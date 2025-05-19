<?php

namespace ExpertsCrm\DataObjects\Models\People;

use ExpertsCrm\Enums\SortOptions;

class ListConfig {
    public string $listId;
    public int $pointer;
    public string $query;
    public string $sort;
    public bool $expanded;
    public int $limit;

    function __construct(int $pointer = 0, string $query = '', string $listId = '', bool $expanded = false, ?string $sort = SortOptions::DESC->value, int $limit = 20) {
        $this->pointer = $pointer;
        $this->query = !empty($query) ? "*".$query."*" : "";
        $this->listId = $listId;
        $this->expanded = $expanded;
        $this->sort = $sort ?: SortOptions::DESC->value;
        $this->limit = $limit;
    }
}