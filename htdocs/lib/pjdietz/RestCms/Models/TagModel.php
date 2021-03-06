<?php

namespace pjdietz\RestCms\Models;

use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;

class TagModel extends RestCmsBaseModel
{
    public $tagId;
    public $tagName;
    public $displayName;
    public $countArticles;

    /**
     * @param int $tagId
     * @throws ResourceException
     * @return TagModel
     */
    public static function init($tagId)
    {
        if (is_numeric($tagId)) {
            return self::initWithId($tagId);
        }
        return self::initWithName($tagId);
    }

    /**
     * @param int $tagId
     * @throws ResourceException
     * @return TagModel
     */
    public static function initWithId($tagId)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    t.displayName,
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

    /**
     * @param string $tagName
     * @throws ResourceException
     * @return TagModel
     */
    public static function initWithName($tagName)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    t.displayName,
    COUNT(a.articleId) as countArticles
FROM
    tag t
    LEFT JOIN articleTag at
        ON t.tagId = at.tagId
    LEFT JOIN article a
        ON at.articleId = a.articleId
WHERE
    t.tagName = :tagName
LIMIT 1;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param int $tagName
     * @throws ResourceException
     * @return TagModel
     */
    public static function createNewTag($tagName)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
INSERT INTO tag (
    dateCreated,
    dateModified,
    tagName
) VALUES (
    NOW(),
    NOW(),
    :tagName
);
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new ResourceException(
                'Unable to store tag. Make sure the tagName is unique.',
                ResourceException::CONFLICT
            );
        }
        $tagId = (int) $db->lastInsertId();
        return self::initWithId($tagId);
    }

    public static function initCollection($options = null)
    {
        if (isset($options['site']) && is_numeric($options['site'])) {
            return self::initCollectionForSite($options['site']);
        }

        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    t.displayName,
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

    public static function initCollectionForSite($siteId)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.tagName,
    t.displayName,
    COUNT(a.articleId) AS countArticle
FROM
    tag t
    JOIN articleTag
        ON t.tagId = articleTag.tagId
    JOIN article a
        ON articleTag.articleId = a.articleId
WHERE
    a.siteId = :siteId
    AND a.statusId = :statusId
GROUP BY
    t.tagId
ORDER BY
    countArticle DESC,
    t.tagName
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->bindValue(':statusId', self::STATUS_PUBLISHED, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    protected function prepareInstance()
    {
        $this->tagId = (int) $this->tagId;
        $this->countArticles = (int) $this->countArticles;
        if (!($this->displayName)) {
            $this->displayName = $this->tagName;
        }
    }
}
