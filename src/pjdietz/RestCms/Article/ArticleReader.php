<?php

namespace pjdietz\RestCms\Article;

use PDO;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

class ArticleReader
{
    private $modelClass;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass = null)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Read an article from the database by ID or slug.
     *
     * @param int|string $id articleId or slug for the resource
     * @param PDO $db Database connection
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return mixed
     */
    public function read($id, PDO $db)
    {
        if (is_numeric($id)) {
            return $this->readWithId($id, $db);
        }
        return $this->readWithSlug($id, $db);
    }

    public function readWithId($id, PDO $db)
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
        return $stmt->fetchObject($this->modelClass);
    }

    public function readWithSlug($slug, PDO $db)
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
        return $stmt->fetchObject($this->modelClass);
    }
}
