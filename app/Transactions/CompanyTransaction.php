<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\Asset\ListConfig as AssetListConfig;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\DataObjects\Transactions\Company\ListConfig;
use ExpertsCrm\Enums\AssetTypes;
use ExpertsCrm\Models\PeopleModel;

defined( 'ABSPATH' ) || exit;

class CompanyTransaction {

    static function list(ListConfig $config) {
        return AssetTransaction::list(
            new AssetListConfig([AssetTypes::COMPANY->value], $config->pointer, $config->query, $config->sort)
        );
    }
    static function store(array $doc, ?string $id) {
       $doc["type"] = AssetTypes::COMPANY->value;
       $doc["search_text"] = json_encode($doc);
       return AssetTransaction::store($doc, $id);
    }

    static function view(ViewConfig $config) {
        $company = AssetTransaction::view($config->id);

        if ($config->expanded) {
            $company["assets"] = AssetTransaction::fetchByObjectId([$config->id]);
            $company["contacts"] = PeopleModel::fetchByCompanyId([$config->id]);
        }

        return $company;
    }
    static function trash(string $id) {
        return AssetTransaction::trash($id);
    }
}