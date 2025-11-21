<?php

namespace WPBulgaria\Chatbot\DataObjects\Models;

defined( 'ABSPATH' ) || exit;

class ListResult
{
    protected array $docs;
    protected bool $hasMore;

    public function __construct(array $docs, bool $hasMore) {
        $this->docs = $docs;
        $this->hasMore = $hasMore;
    }

    public function toArray(): array {
        return [
            "docs" => $this->docs,
            "hasMore" => $this->hasMore,
        ];
    }
}