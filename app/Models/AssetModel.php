<?php

namespace ExpertsCrm\Models;
use ExpertsCrm\DataObjects\Models\Asset\ListConfig;
use ExpertsCrm\DataObjects\Models\ListResult;
use function ExpertsCrm\createSearchable;
use function ExpertsCrm\genId;

defined( 'ABSPATH' ) || exit;

class AssetModel extends BaseModel {
    const PER_PAGE = 20;

    static function getTable() {
        global $wpdb;
        return $wpdb->prefix."experts_crm_assets";
    }

    static function list(ListConfig $config) {
        global $wpdb;

        $table = self::getTable();
        $in = implode("','", $config->types);
        $inWhere = !empty($config->types) ? "AND type IN ('$in')" : "";


        $offset = $config->pointer * self::PER_PAGE;
        $count = self::PER_PAGE + 1;
        $search = !empty($config->query) ? "AND MATCH(search_text) AGAINST (%s IN BOOLEAN MODE)" : "";
        $sort = $config->sort;

        $query = $wpdb->prepare("SELECT * FROM $table WHERE removed_at IS NULL $inWhere $search ORDER BY created_at $sort LIMIT $offset, $count", $config->query);


        $rows = $wpdb->get_results( $query, ARRAY_A);
        if (empty($rows)) {
            return new ListResult([], false);
        }

        $docs = [];
        foreach ($rows as $key => $row) {
            if ($key >= self::PER_PAGE) {
                break;
            }
            $doc = json_decode($row['doc'], true);
            if (!empty($doc)) {
                $docs[] = $doc;
            }
        }

        return new ListResult($docs, count($rows) > self::PER_PAGE);
    }
    static function store(array $doc, ?string $id) {
        global $wpdb;

        $isNew = empty($id);
        $id = $isNew ? genId() : sanitize_text_field($id);
        $doc["_id"] = $id;
        $doc["modifiedAt"] = date(DATE_ATOM);
        if ($isNew) {
            $doc["createdAt"] = date(DATE_ATOM);
        }

        if (empty($id)) {
            throw new \Exception(self::INVALID_DATA_MSG, self::INVALID_DATA_CODE);
        }


        if (!empty($doc["search_text"])) {
            $search = $doc["search_text"];
            unset($doc["search_text"]);
        } else {
            $search = createSearchable(["name", "title", "description", "content", "type"], $doc);
        }



        if (!$isNew) {
            $result = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE ".self::getTable()." 
                    SET doc=%s, modified_at=%s, parent_id=%s, parent_type=%s, type=%s, search_text=%s
                    WHERE id=%s AND removed_at IS NULL",
                    json_encode($doc), $doc["modifiedAt"], $doc["objectId"] ?? "", $doc["objectType"] ?? "", $doc["type"] ?? "", $search, $id ) );
        } else {
            $result = $wpdb->insert(self::getTable(),
                [   "id" => $id,
                    "doc" => json_encode($doc),
                    "created_at" => $doc["createdAt"],
                    "modified_at" => $doc["modifiedAt"],
                    "parent_id" => $doc["objectId"],
                    "parent_type" => $doc["objectType"],
                    "search_text" => $search,
                    "type" => $doc["type"],
                ]);
        }

        if ($result === false) {
            throw new \Exception(self::SERVER_ERROR_MSG, self::SERVER_ERROR_CODE);
        }

        return $id;
    }
    static function view(string $id) {
        global $wpdb;

        $table = self::getTable();
        $query = $wpdb->prepare("SELECT * FROM $table WHERE id=%s AND removed_at IS NULL;", $id);
        $row = $wpdb->get_row( $query, ARRAY_A );
        if (empty($row)) {
            throw new \Exception(self::NOT_FOUND_MSG, self::NOT_FOUND_CODE);
        }

        $doc = json_decode($row['doc'], true);
        if (empty($doc)) {
            throw new \Exception(self::SERVER_ERROR_MSG, self::SERVER_ERROR_CODE);
        }


        try {
            $doc['actions'] = ActionModel::fetchByObjectId([$doc["_id"]]);
        } catch (\Exception $e) {}

        return $doc;

    }
    static function trash(string $id) {
        global $wpdb;
        $result = $wpdb->update( self::getTable(), ["removed_at" => date(DATE_ATOM)], ["id" => $id] );
        return is_int($result);
    }

    static function remove(string $id) {
        global $wpdb;
        $result = $wpdb->delete( self::getTable(), ["id" => $id] );
        return is_int($result);
    }

    static function bulkTrash(array $ids) {
        global $wpdb;
        $in = implode("','", $ids);
        $result = $wpdb->query( $wpdb->prepare("UPDATE ".self::getTable()." SET removed_at=%s WHERE id IN ('$in')", date(DATE_ATOM)));
        return is_int($result);
    }

    static function bulkTrashByObjectId(array $ids) {
        global $wpdb;
        $in = implode("','", $ids);
        $result = $wpdb->query( $wpdb->prepare("UPDATE ".self::getTable()." SET removed_at=%s WHERE parent_id IN ('$in')", date(DATE_ATOM)));
        return is_int($result);
    }

    static function bulkRemoveByObjectId(array $ids) {
        global $wpdb;
        $in = implode("','", $ids);
        $result = $wpdb->query( $wpdb->prepare("DELETE FROM ".self::getTable()." WHERE parent_id IN ('$in') AND removed_at IS NOT NULL"));
        return is_int($result);
    }

    static function bulkRemove(array $ids) {
        global $wpdb;
        $in = implode("','", $ids);
        $result = $wpdb->query( $wpdb->prepare("DELETE FROM ".self::getTable()." WHERE id IN ('$in') AND removed_at IS NOT NULL", date(DATE_ATOM)));
        return is_int($result);
    }

    static function fetchAll(array $ids, $trashed = false) {
        global $wpdb;
        $in = implode("','", $ids);
        $andWhere = !$trashed ? "AND removed_at IS NULL" : "";
        $query = $wpdb->prepare("SELECT * FROM ".self::getTable()." WHERE id IN ('$in') $andWhere");
        $rows = $wpdb->get_results($query, ARRAY_A);

        $docs = [];
        foreach ($rows as $row) {
            $doc = json_decode($row['doc'], true);
            if (!empty($doc)) {
                $docs[] = $doc;
            }
        }

        return $docs;
    }

    static function fetchByObjectId(array $ids, $trashed = false) {
        global $wpdb;
        $in = implode("','", $ids);
        $andWhere = !$trashed ? "AND removed_at IS NULL" : "";
        $query = $wpdb->prepare("SELECT * FROM ".self::getTable()." WHERE parent_id IN ('$in') $andWhere");
        $rows = $wpdb->get_results($query, ARRAY_A);

        $docs = [];
        foreach ($rows as $row) {
            $doc = json_decode($row['doc'], true);
            if (!empty($doc)) {
                $docs[] = $doc;
            }
        }

        return $docs;
    }
}