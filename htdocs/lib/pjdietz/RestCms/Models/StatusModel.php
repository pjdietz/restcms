<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\Helpers\StatusHelper;

class StatusModel extends RestCmsBaseModel
{
    public $statusId;

    protected function prepareInstance()
    {
        $this->statusId = (int) $this->statusId;
    }

    public static function initWithId($statusId)
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

        return new self($stmt->fetchObject());
    }

    public static function initWithSlug($statusSlug)
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

        return new self($stmt->fetchObject());
    }

    public static function initCollection($options)
    {
        $tmpStatus = new StatusHelper($options);

        $query = <<<SQL
SELECT
    s.statusId,
    s.statusSlug AS `slug`,
    s.statusName AS `name`
FROM status s

SQL;

        if ($tmpStatus->isRequired()) {
            $query .= <<<QUERY
    JOIN tmpStatus ts
        ON s.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());

        // Drop temporary tables.
        $tmpStatus->drop();

        return $collection;
    }
}
