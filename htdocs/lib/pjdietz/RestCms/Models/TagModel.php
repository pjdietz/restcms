<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;

class TagModel extends RestCmsBaseModel
{
    public $tagId;
    public $tagName;
    public $countArticles;

    public static function init($tagId)
    {
        if (is_numeric($tagId)) {
            return self::initWithId($tagId);
        }
    }

    public static function initWithId($tagId)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    COUNT(a.articleId) as countArticles
FROM
    tag t
    LEFT JOIN articleTag at
        ON t.tagId = at.tagId
    LEFT JOIN article a
        ON at.articleId = a.articleId
WHERE
    t.tagId = :tagId
LIMIT 1;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }
        return $stmt->fetchObject(get_called_class());
    }

    public static function initCollection()
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    COUNT(a.articleId) as countArticles
FROM
    tag t
    LEFT JOIN articleTag at
        ON t.tagId = at.tagId
    LEFT JOIN article a
        ON at.articleId = a.articleId
GROUP BY
    t.tagId
ORDER BY
    countArticles DESC,
    t.tagName;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    protected function prepareInstance()
    {
        $this->tagId = (int) $this->tagId;
        $this->countArticles = (int) $this->countArticles;
    }
}
