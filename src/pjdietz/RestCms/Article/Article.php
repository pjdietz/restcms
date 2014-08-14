<?php

namespace pjdietz\RestCms\Article;

use PDO;
use pjdietz\RestCms\Model;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

/**
 * Represents an article
 */
class Article extends Model
{
    public $articleId;
    public $slug;

    /**
     * Read an article from the database by ID or slug.
     *
     * @param int|string $id articleId or slug for the resource
     * @param PDO $db Database connection
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return Article
     */
    public static function init($id, PDO $db)
    {
        if (is_numeric($id)) {
            return self::initWithId($id, $db);
        }
        return self::initWithSlug($id, $db);
    }

    public static function initWithId($id, PDO $db)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    a.slug
FROM
    article a
WHERE
    a.articleId = :articleId
LIMIT 1;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject(get_called_class());
    }

    public static function initWithSlug($slug, PDO $db)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    a.slug
FROM
    article a
WHERE
    a.slug = :slug
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
        $this->articleId = (int) $this->articleId;
    }
}
