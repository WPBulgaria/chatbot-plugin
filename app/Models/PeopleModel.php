<?php

namespace ExpertsCrm\Models;
use ExpertsCrm\DataObjects\Models\People\ListConfig;
use ExpertsCrm\DataObjects\Models\ListResult;
use ExpertsCrm\Enums\SortOptions;
use function ExpertsCrm\genId;

defined( 'ABSPATH' ) || exit;

class PeopleModel extends BaseModel {
    const PER_PAGE = 20;

    static function getTable() {
        global $wpdb;
        return $wpdb->prefix."experts_crm_people";
    }


    static function cnt(array $listIds) {
        global $wpdb;
        $in = implode("','", $listIds);
        $listId = !empty($config->listId) ? "AND list_id IN ('$in')" : "";
        $table = self::getTable();
        $query = $wpdb->prepare("SELECT list_id, COUNT(*) as people FROM $table WHERE removed_at IS NULL $listId GROUP BY list_id");

        $result = $wpdb->get_results( $query, ARRAY_A);
        $lists = [];
        if (is_array($result)) {
            foreach ($result as $row) {
                $lists[$row['list_id']] = (int) $row['people'];
            }
        }

        return $lists;
    }

    static function list(ListConfig $config) {
        global $wpdb;

        $table = self::getTable();

        $count = $config->limit + 1;
        $offset = $config->pointer * $config->limit;
        $search = !empty($config->query) ? "AND MATCH(search_text) AGAINST(%s IN BOOLEAN MODE)" : '';
        $listId = !empty($config->listId) ? "AND list_id=%s" : "";
        $sort = $config->sort ?? SortOptions::DESC->value;

        $queryStr = "SELECT * FROM $table WHERE removed_at IS NULL $search $listId ORDER BY created_at $sort LIMIT $offset, $count";

        if ($search && $listId) {
            $query = $wpdb->prepare($queryStr, $config->query, $config->listId);
        } elseif ($search) {
            $query = $wpdb->prepare($queryStr, $config->query);
        } else {
            $query = $wpdb->prepare($queryStr, $config->listId);
        }



        $rows = $wpdb->get_results( $query, ARRAY_A);
        if (empty($rows)) {
            return new ListResult([], false);
        }

        $listIds = [];
        foreach ($rows as $key => $row) {
            if ($key === $config->limit) {
                break;
            }
            $doc = json_decode($row['doc'], true);
            if (!empty($doc)) {
                if ($row['list_id']) {
                    $key = $row['list_id'].$row['id'];
                    $listIds[$row['list_id']][] = $key;
                    $map[$key] = $doc;
                } else {
                    $map[$row['id']] = $doc;
                }
            }
        }

        if (!empty($listIds)) {
            $ids = array_keys($listIds);
            $lists = ListModel::fetchAll($ids);

            foreach ($lists as $list) {
                $keys = $listIds[$list['_id']];
                foreach ($keys as $key) {
                    if (isset($map[$key])) {
                        $map[$key]["list"] = $list;
                    }
                }
            }
        }

        return new ListResult(array_values($map), count($rows) > self::PER_PAGE);
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

        $search = json_encode($doc);

        if (!$isNew) {
            $result = $wpdb->query( $wpdb->prepare("UPDATE ".self::getTable()." SET doc=%s, modified_at=%s, company_id=%s, list_id=%s, search_text=%s WHERE id=%s AND removed_at IS NULL", json_encode($doc), $doc["modifiedAt"], $doc["companyId"] ?? "", $doc["listId"] ?? "", $search, $id ) );
        } else {
            $result = $wpdb->insert(self::getTable(), [ "id" => $id, "doc" => json_encode($doc), "created_at" => $doc["createdAt"], "list_id" => $doc['listId'], "modified_at" => $doc["modifiedAt"], "search_text" => $search]);
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

        if ($row["list_id"]) {
            try {
                $doc["list"] = ListModel::view($row["list_id"]);
            } catch (\Exception $e) {}
        }

        return $doc;

    }

    static function fetchByCompanyId (array $ids, bool $trashed = false)
    {
        global $wpdb;
        $in = implode("','", $ids);
        $andWhere = !$trashed ? "AND removed_at IS NULL" : "";
        $query = $wpdb->prepare("SELECT * FROM ".self::getTable()." WHERE company_id IN ('$in') $andWhere");
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


    static function trash(string $id) {
        global $wpdb;
        /**
         *
         * Transaction
         * await db.actions.bulkDelete(actions.map(a => a._id));
         * await db.assets.bulkDelete(assetIds);
         * await db.lists.delete(request.data);
         */
        $result = $wpdb->update( self::getTable(), ["removed_at" => date(DATE_ATOM)], ["id" => $id] );
        return is_int($result);
    }

}