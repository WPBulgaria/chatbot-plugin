<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\DataObjects\Transactions\List\ViewConfig as ListViewConfig;
use ExpertsCrm\DataObjects\Transactions\People\ViewConfig as PeopleViewConfig;
use ExpertsCrm\DataObjects\Transactions\Project\ListConfig;
use ExpertsCrm\Enums\AssetTypes;
use ExpertsCrm\Enums\ObjectTypes;

defined( 'ABSPATH' ) || exit;

class ProjectTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(new AssetListConfig([AssetTypes::PROJECT->value], $config->pointer, $config->query, $config->sort));
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::PROJECT->value;
       return AssetTransaction::store($doc, $id);
    }

    static function view(ViewConfig $config) {
        $project = AssetTransaction::view($config->id);

        if ($config->expanded) {
            $project["assets"] = AssetTransaction::fetchByObjectId([$config->id]);

            try {
                switch ($project["objectType"]) {
                    case ObjectTypes::COMPANY->value:
                        $project['object'] = AssetTransaction::view($project["objectId"]);
                        break;
                    case ObjectTypes::PERSON->value:
                        $project['object'] = PeopleTransaction::view(new PeopleViewConfig($project["objectId"], false));
                }
            } catch (\Exception $e) {
            }
        }

        return $project;
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}