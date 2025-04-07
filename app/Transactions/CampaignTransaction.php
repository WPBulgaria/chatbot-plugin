<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\DataObjects\Transactions\Campaign\ListConfig;
use ExpertsCrm\Enums\AssetTypes;

defined( 'ABSPATH' ) || exit;

class CampaignTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::CAMPAIGN->value], $config->pointer, $config->query, $config->sort));
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::CAMPAIGN->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(ViewConfig $config) {
        $campaign = AssetTransaction::view($config->id);

        if ($config->expanded) {
            $campaign["assets"] = AssetTransaction::fetchByObjectId([$config->id]);
        }

        return $campaign;
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}