<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\People\ListConfig;
use ExpertsCrm\DataObjects\Models\ListResult;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\Models\PeopleModel;

defined( 'ABSPATH' ) || exit;

class PeopleTransaction {
    static function list (ListConfig $config)
    {
        $result = PeopleModel::list($config);

        if (!$config->expanded) {
            return $result;
        }

        $map = [];
        $people = $result->toArray()['people'];
        foreach ($people as $person) {
            $map[$person["_id"]] = $person;
        }


        $assets = AssetTransaction::fetchByObjectId(array_keys($map));
        foreach ($assets as $asset) {
            if (isset($map[$asset["objectId"]])) {
                $map[$asset["objectId"]]["assets"][] = $assets;
            }
        }

        return new ListResult(array_values($map), $result->toArray()["hasMore"]);
    }

    static function view (ViewConfig $config)
    {
        $person = PeopleModel::view($config->id);

        if (!$config->expanded) {
            return $person;
        }

        try {
            $person["assets"] = AssetTransaction::fetchByObjectId([$config->id]);
            if (!empty($person["companyId"])) {
                $person["company"] = CompanyTransaction::view(new ViewConfig($person["companyId"], false));
            }

        } catch (\Exception $e) {

        }

        return $person;
    }

    static function store(array $doc, ?string $id)
    {
        return PeopleModel::store($doc, $id);
    }
    static function trash(string $id) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $result = PeopleModel::trash($id);
        $actions_result = AssetTransaction::bulkTrashByObjectId([$id]);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }

    static function remove(string $id) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $result = PeopleModel::remove($id);
        $actions_result = AssetTransaction::bulkRemoveByObjectId([$id]);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }
}