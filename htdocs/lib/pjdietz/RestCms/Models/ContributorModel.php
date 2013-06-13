<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;

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

    protected function prepareInstance()
    {
        $this->userId = (int) $this->userId;
    }
}
