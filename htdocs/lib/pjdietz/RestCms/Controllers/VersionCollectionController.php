<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class VersionCollectionController extends RestCmsBaseController
{
    /**
     * Create a collection of Article Versions filtered by the given options array.
     *
     * @param int $articleId
     * @return array|null
     */
    public function read($articleId)
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

}
