<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class VersionItemController extends RestCmsBaseController
{
    /**
     * @param string $articleId
     * @param string $versionId
     * @return VersionCollectionController
     */
    public function read($articleId, $versionId)
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
            $this->data = $rows[0];
            return $this->data;
        } else {
            return null;
        }
    }
}
