<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class StatusController extends RestCmsBaseController
{
    const STATUS_MODEL = 'pjdietz\RestCms\Models\StatusModel';

    public function readCollection($options = null)
    {
        $useTmpStatus = $this->createTmpStatus($options);

        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusSlug AS `slug`,
    s.statusName AS `name`
FROM status s

SQL;

        if ($useTmpStatus) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON s.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_CLASS, self::STATUS_MODEL);

        // Drop temporary tables.
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $this->data;
    }

    public function readItem($statusId)
    {
        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusSlug AS `slug`,
    s.statusName AS `name`
FROM status s
WHERE 1 = 1
    AND s.statusId = :statusId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetchObject(self::STATUS_MODEL);
    }

    public function readItemBySlug($statusSlug)
    {
        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusSlug AS `slug`,
    s.statusName AS `name`
FROM status s
WHERE 1 = 1
    AND s.statusSlug = :statusSlug
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':statusSlug', $statusSlug, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetchObject(self::STATUS_MODEL);
    }

}