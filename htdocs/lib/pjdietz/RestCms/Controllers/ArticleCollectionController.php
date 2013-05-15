<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;
use PDO;

/**
 * Class for reading and writing Article and performing database interactions.
 */
class ArticleCollectionController extends ArticleController
{
    public function __construct($options = null)
    {
        $this->readFromDatabase($options);
    }

    /**
     * Create a collection of Articles filtered by the given options array.
     *
     * @param array $options
     * @return ArticleCollectionController|null
     */
    public static function newFromOptions($options)
    {
        $controller = new ArticleCollectionController();
        $controller->readFromDatabase($options);
        return $controller;
    }

    private function readFromDatabase($options)
    {
        $query = <<<'SQL'
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName AS status,
    av.title,
    av.excerpt
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
    JOIN status s
        ON a.statusId = s.statusId

SQL;

        if ($this->createTmpStatus($options)) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON a.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
