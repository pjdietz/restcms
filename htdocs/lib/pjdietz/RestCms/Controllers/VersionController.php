<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class VersionController
{
    const VERSION_MODEL = 'pjdietz\RestCms\Models\VersionModel';

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
    a.articleId,
    v.versionId,
    v.dateCreated,
    IF (a.currentVersionId = v.versionId, 1, 0) AS `isCurrent`
FROM
    article a
    JOIN version v
        ON a.articleId = v.parentArticleId
        AND a.articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_CLASS, self::VERSION_MODEL);

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
    v.versionId,
    v.dateCreated,
    v.title,
    v.content,
    v.excerpt,
    v.notes,
    IF (a.currentVersionId = v.versionId, 1, 0) AS `isCurrent`
FROM
    article a
    JOIN version v
        ON a.articleId = v.parentArticleId
        AND a.articleId = :articleId
WHERE 1 = 1
    AND v.versionId = :versionId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':versionId', $versionId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_CLASS, self::VERSION_MODEL);

        if ($rows && count($rows) > 0) {
            return $rows[0];
        } else {
            return null;
        }
    }
}
