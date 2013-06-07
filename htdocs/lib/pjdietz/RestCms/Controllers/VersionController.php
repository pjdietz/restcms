<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class VersionController
{
    /**
     * Read the collection of version for the given article.
     *
     * @param int $articleId
     * @return array|null
     */
    public function readCollection($articleId)
    {
        $query = <<<SQL
SELECT
    av.articleVersionId,
    av.dateCreated
FROM
    article a
    JOIN articleVersion av
        ON a.articleId = av.parentArticleId
        AND a.articleId = :articleId

SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            return $rows;
        } else {
            return null;
        }
    }

    /**
     * Read the version for the given article and version Ids.
     *
     * @param int $articleId
     * @param int $versionId
     * @return object|null
     */
    public function readItem($articleId, $versionId)
    {
        $query = <<<SQL
SELECT
    av.articleVersionId,
    av.dateCreated,
    av.title,
    av.content,
    av.excerpt,
    av.notes
FROM
    article a
    JOIN articleVersion av
        ON a.articleId = av.parentArticleId
        AND a.articleId = :articleId
WHERE 1 = 1
    AND av.articleVersionId = :versionId;

SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':versionId', $versionId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            return $rows[0];
        } else {
            return null;
        }
    }
}
