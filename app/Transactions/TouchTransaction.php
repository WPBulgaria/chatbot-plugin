<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Touch\ListConfig;
use ExpertsCrm\Enums\AssetTypes;

defined( 'ABSPATH' ) || exit;

class TouchTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::TOUCH->value], $config->pointer, $config->query, $config->sort));
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::TOUCH->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(string $id) {
        return AssetTransaction::view($id);
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}