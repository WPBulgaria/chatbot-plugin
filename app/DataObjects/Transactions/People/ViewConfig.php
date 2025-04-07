<?php

namespace ExpertsCrm\DataObjects\Transactions\People;

defined( 'ABSPATH' ) || exit;

class ViewConfig extends \ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig
{
    public string $id;
    public bool $expanded;

    function __construct(string $id, bool $expanded) {
       $this->id = $id;
       $this->expanded = $expanded;
    }
}