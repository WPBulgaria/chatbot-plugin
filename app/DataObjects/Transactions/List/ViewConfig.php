<?php

namespace ExpertsCrm\DataObjects\Transactions\List;

defined( 'ABSPATH' ) || exit;

class ViewConfig {
    public string $id;
    public bool $expanded;

    function __construct(string $id, bool $expanded) {
       $this->id = $id;
       $this->expanded = $expanded;
    }
}