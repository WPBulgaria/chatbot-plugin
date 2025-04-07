<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig as AssetViewConfig;
use ExpertsCrm\DataObjects\Transactions\List\ViewConfig as ListViewConfig;
use ExpertsCrm\DataObjects\Transactions\People\ViewConfig as PeopleViewConfig;

use ExpertsCrm\DataObjects\Transactions\Note\ListConfig;

use ExpertsCrm\Enums\AssetTypes;
use ExpertsCrm\Enums\ObjectTypes;

defined( 'ABSPATH' ) || exit;

class NoteTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::NOTE->value], $config->pointer, $config->query, $config->sort));
    }

    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::NOTE->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(AssetViewConfig $config) {
        $note = AssetTransaction::view($config->id);
        if (!$config->expanded) {
            return $note;
        }

        try {
            switch ($note["objectType"]) {
                case ObjectTypes::PROJECT->value:
                case ObjectTypes::CAMPAIGN->value:
                case ObjectTypes::OPPORTUNITY->value:
                case ObjectTypes::COMPANY->value:
                case ObjectTypes::TASK->value:
                    $note['object'] = AssetTransaction::view($note["objectId"]);
                    break;
                case ObjectTypes::LIST->value:
                    $note['object'] = ListTransaction::view(new ListViewConfig($note["objectId"], false));
                    break;
                case ObjectTypes::PERSON->value:
                    $note['object'] = PeopleTransaction::view(new PeopleViewConfig($note["objectId"], false));
            }
        } catch (\Exception $e) {
        }

        return $note;
    }

    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}