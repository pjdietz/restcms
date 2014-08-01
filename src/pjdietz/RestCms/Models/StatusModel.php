<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\TempTable\StatusTempTable;
use pjdietz\RestCms\Exceptions\ResourceException;

class StatusModel extends RestCmsBaseModel
{
    public $statusId;

    /**
     * Read a status from the database by ID or slug.
     *
     * @param int|string $statusId
     * @return StatusModel
     * @throws ResourceException
     */
    public static function init($statusId)
    {
        if (is_numeric($statusId)) {
            return self::initWithId($statusId);
        }
        return self::initWithSlug($statusId);
    }

    /**
     * @param $statusId
     * @return StatusModel
     * @throws ResourceException
     */
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
            throw new ResourceException("No status with id {$statusId}", ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param $statusSlug
     * @return StatusModel
     * @throws ResourceException
     */
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
            throw new ResourceException("No status with slug {$statusSlug}", ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param array $options
     * @return array
     */
    public static function initCollection(array $options)
    {
        $tmpStatus = new StatusTempTable($options);

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

    protected function prepareInstance()
    {
        $this->statusId = (int) $this->statusId;
    }
}
