<?php

namespace WPBulgaria\Chatbot\Transformers;

defined( 'ABSPATH' ) || exit;

interface ITransformer {
    static function apply($data);
}
