<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;

class ContributorModel extends RestCmsBaseModel
{
    /** @var int */
    public $userId;

    /**
     * Read a collection of Articles filtered by the given options array.
     *
     * @param int $articleId
     * @return array|null
     */
    public static function initCollection($articleId)
    {
        $query = <<<SQL
SELECT
    u.userId,
    u.username,
    u.displayName
FROM
    contributor c
    JOIN user u
        ON c.userId = u.userId
WHERE 1 = 1
    AND c.articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function initWithId($articleId, $userId)
    {
        $query = <<<SQL
SELECT
    u.userId,
    u.username,
    u.displayName
FROM
    contributor c
    JOIN user u
        ON c.userId = u.userId
        AND u.userId = :userId
WHERE 1 = 1
    AND c.articleId = :articleId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
    }

    protected function prepareInstance()
    {
        $this->userId = (int) $this->userId;
    }
}
