<?php

namespace ExpertsCrm\Transactions;


use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\DataObjects\Transactions\Opportunity\ListConfig;
use ExpertsCrm\DataObjects\Transactions\People\ViewConfig as PeopleViewConfig;
use ExpertsCrm\Enums\AssetTypes;
use ExpertsCrm\Enums\ObjectTypes;

defined( 'ABSPATH' ) || exit;

class OpportunityTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::OPPORTUNITY->value], $config->pointer, $config->query, $config->sort));
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::OPPORTUNITY->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(ViewConfig $config) {
        $opportunity = AssetTransaction::view($config->id);

        if ($config->expanded) {
            $opportunity["assets"] = AssetTransaction::fetchByObjectId([$config->id]);

            try {
                switch ($opportunity["objectType"]) {
                    case ObjectTypes::COMPANY->value:
                        $opportunity['object'] = AssetTransaction::view($opportunity["objectId"]);
                        break;
                    case ObjectTypes::PERSON->value:
                        $opportunity['object'] = PeopleTransaction::view(new PeopleViewConfig($opportunity["objectId"], false));
                }
            } catch (\Exception $e) {
            }
        }

        return $opportunity;
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}