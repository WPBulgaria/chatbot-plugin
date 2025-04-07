<?php

namespace ExpertsCrm\Transactions;

use Couchbase\View;
use ExpertsCrm\DataObjects\Models\Asset\ListConfig;
use ExpertsCrm\DataObjects\Models\ListResult;
use ExpertsCrm\DataObjects\Transactions\Asset\ViewConfig;
use ExpertsCrm\Models\ActionModel;
use ExpertsCrm\Models\AssetModel;

defined( 'ABSPATH' ) || exit;

class AssetTransaction {
    static function list (ListConfig $config)
    {
        $result = AssetModel::list($config);
        $assets = $result->toArray()["docs"];
        $map = [];
        foreach ($assets as $asset) {
            $map[$asset["_id"]] = $asset;
        }


        $actions = ActionModel::fetchByObjectId(array_keys($map));
        foreach ($actions as $action) {
            if (isset($map[$action["objectId"]])) {
                $map[$action["objectId"]]["actions"][] = $action;
            }
        }

        return new ListResult(array_values($map), $result->toArray()["hasMore"]);
    }

    static function fetchByObjectId (array $ids)
    {
        $assets = AssetModel::fetchByObjectId($ids);
        $map = [];
        foreach ($assets as $asset) {
            $map[$asset["_id"]] = $asset;
        }


        $actions = ActionModel::fetchByObjectId(array_keys($map));
        foreach ($actions as $action) {
            if (isset($map[$action["objectId"]])) {
                $map[$action["objectId"]]["actions"][] = $action;
            }
        }

        return array_values($map);
    }


    static function view (string $id)
    {
        $asset = AssetModel::view($id);
        $asset["actions"] = ActionModel::fetchByObjectId([$asset["_id"]]);
        return $asset;
    }

    static function store(array $doc, ?string $id)
    {
        global $wpdb;

        try {
            $wpdb->query("START TRANSACTION");
            $actions = $doc["actions"] ?? [];
            unset($doc["actions"]);
            $assetId = AssetModel::store($doc, $id);

            if (!empty($actions)) {
                foreach ($actions as $key => $action) {
                    $action["objectId"] = $assetId;
                    $action["objectType"] = $doc["type"];
                    ActionModel::store($action, $action["_id"]);
                }
            }

            $wpdb->query("COMMIT");
            return $assetId;
        } catch (\Exception $e) {
            $wpdb->query("ROLLBACK");
            throw $e;
        }
    }
    static function trash(string $id) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $result = AssetModel::trash($id);
        $actions_result = ActionModel::bulkTrashByObjectId([$id]);

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
        $result = AssetModel::remove($id);
        $actions_result = ActionModel::bulkRemoveByObjectId([$id]);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }

    static function bulkTrash(array $ids) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $result = AssetModel::bulkTrash($ids);
        $actions_result = ActionModel::bulkTrashByObjectId($ids);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }

    static function bulkTrashByObjectId(array $ids) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $assets = AssetModel::fetchByObjectId($ids);
        $assetsIds = array_map(fn($asset) => $asset["id"], $assets);

        $result = AssetModel::bulkTrashByObjectId($ids);
        $actions_result = ActionModel::bulkTrashByObjectId($assetsIds);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }

    static function bulkRemoveByObjectId(array $ids) {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $assets = AssetModel::fetchByObjectId($ids);
        $assetsIds = array_map(fn($asset) => $asset["id"], $assets);

        $result = AssetModel::bulkRemoveByObjectId($ids);
        $actions_result = ActionModel::bulkRemoveByObjectId($assetsIds);

        if ($result && $actions_result) {
            $wpdb->query("COMMIT");
            return true;
        } else {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }
}