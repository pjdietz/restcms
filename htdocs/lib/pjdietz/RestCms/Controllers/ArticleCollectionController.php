<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;
use PDO;

/**
 * Class for reading and writing Article and performing database interactions.
 */
class ArticleCollectionController extends RestCmsBaseController
{
    /**
     * Create a collection of Articles filtered by the given options array.
     *
     * @param array $options
     * @return array|null
     */
    public function readFromOptions($options)
    {
        $useTmpArticleId = $this->createTmpArticleId($options);
        $useTmpStatus = $this->createTmpStatus($options);

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

        if ($useTmpArticleId) {
            $query .= <<<'QUERY'
    JOIN tmpArticleId ta
        ON a.articleId = ta.articleId

QUERY;
        }

        if ($useTmpStatus) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON a.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Drop temporary tables.
        if ($useTmpArticleId) {
            $this->dropTmpArticleId();
        }
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $this->data;
    }
}
