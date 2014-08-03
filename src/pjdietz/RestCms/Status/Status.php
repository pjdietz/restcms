<?php

namespace pjdietz\RestCms\Status;

use PDO;
use pjdietz\RestCms\Model;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

class Status extends Model
{
    public $statusId;
    public $slug;
    public $name;

    /**
     * @param int $statusId
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Status
     */
    public static function init($statusId, PDO $db)
    {
        if (is_numeric($statusId)) {
            return self::initWithId($statusId, $db);
        }
        return self::initWithSlug($statusId, $db);
    }

    /**
     * @param int $statusId
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Status
     */
    public static function initWithId($statusId, PDO $db)
    {
        $query = <<<SQL
SELECT
    s.statusId,
    s.slug,
    s.name
FROM
    status s
WHERE
    s.statusId = :statusId
LIMIT 1;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param string $slug
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Status
     */
    public static function initWithSlug($slug, PDO $db)
    {
        $query = <<<SQL
SELECT
    s.statusId,
    s.slug,
    s.name
FROM
    status s
WHERE
    s.slug = :slug
LIMIT 1;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Allow the instance to update its members after construction or deserialization.
     * @return void
     */
    protected function prepareInstance()
    {
        $this->statusId = (int) $this->statusId;
    }
}
