<?php

namespace ExpertsCrm\Transformers;

defined( 'ABSPATH' ) || exit;

interface ITransformer {
    static function apply($data);
}
