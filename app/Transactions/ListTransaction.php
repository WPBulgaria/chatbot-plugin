<?php

namespace ExpertsCrm\Transactions;

use ExpertsCrm\DataObjects\Models\List\ListConfig;
use ExpertsCrm\DataObjects\Models\ListResult;
use ExpertsCrm\DataObjects\Transactions\List\ViewConfig;
use ExpertsCrm\Models\ActionModel;
use ExpertsCrm\Models\AssetModel;
use ExpertsCrm\Models\ListModel;
use ExpertsCrm\Models\PeopleModel;

defined( 'ABSPATH' ) || exit;

class ListTransaction {
    static function list (ListConfig $config)
    {
        $lists = ListModel::list($config);
        $listIds = array_map(function ($item) { return $item["_id"];}, $lists->toArray()['docs']);
        $counted = PeopleModel::cnt($listIds);

        $docs = [];
        $data = $lists->toArray()['docs'];

        foreach ($data as $doc) {
           $doc['people'] = $counted[$doc['_id']];
           $docs[] = $doc;
        }

        $lists = new ListResult($docs, $lists->toArray()['hasMore']);

        if (!$config->expanded) {
            return $lists;
        }

        $map = [];
        foreach ($lists as $list) {
            $map[$list["_id"]] = $list;
        }




        $assets = AssetTransaction::fetchByObjectId(array_keys($map));
        foreach ($assets as $asset) {
            if (isset($map[$asset["objectId"]])) {
                $map[$asset["objectId"]]["assets"][] = $assets;
            }
        }

        return array_values($map);
    }

    static function view (ViewConfig $config)
    {
        $list = ListModel::view($config->id);
        if ($config->expanded) {
            $list["assets"] = AssetTransaction::fetchByObjectId([$config->id]);
        }

        return $list;
    }

    static function store(array $doc, ?string $id)
    {
        return ListModel::store($doc, $id);
    }
    static function trash(string $id) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $result = ListModel::trash($id);
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
        $result = ListModel::remove($id);
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