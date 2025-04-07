<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig as AssetViewConfig;
use ExpertsCrm\DataObjects\Transactions\List\ViewConfig as ListViewConfig;
use ExpertsCrm\DataObjects\Transactions\People\ViewConfig as PeopleViewConfig;
use ExpertsCrm\DataObjects\Transactions\Task\ListConfig;
use ExpertsCrm\Enums\AssetTypes;
use ExpertsCrm\Enums\ObjectTypes;

defined( 'ABSPATH' ) || exit;

class TaskTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::TASK->value], $config->pointer, $config->query, $config->sort));
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::TASK->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(AssetViewConfig $config) {
        $task = AssetTransaction::view($config->id);
        if (!$config->expanded) {
            return $task;
        }


        try {
            $task["assets"] = AssetTransaction::fetchByObjectId([$config->id]);

            switch ($task["objectType"]) {
                case ObjectTypes::PROJECT->value:
                case ObjectTypes::CAMPAIGN->value:
                case ObjectTypes::OPPORTUNITY->value:
                case ObjectTypes::COMPANY->value:
                    $task['object'] = AssetTransaction::view($task["objectId"]);
                    break;
                case ObjectTypes::LIST->value:
                    $task['object'] = ListTransaction::view(new ListViewConfig($task["objectId"], false));
                    break;
                case ObjectTypes::PERSON->value:
                    $task['object'] = PeopleTransaction::view(new PeopleViewConfig($task["objectId"], false));
            }

        } catch (\Exception $e) {
        }

        return $task;
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}