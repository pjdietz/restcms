<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Connections\Database;

class ArticleModel extends RestCmsBaseModel
{
    public $articleId;

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
    }

    public static function newById($articleId)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName as status,
    v.title,
    v.content,
    v.excerpt,
    v.notes
FROM
    article a
    JOIN version v
        ON a.currentVersionId = v.versionId
        AND a.articleId = :articleId
    JOIN status s
        ON a.statusId = s.statusId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() !== 1) {
            return null;
        }

        return new self($stmt->fetchObject());
    }
}
