<?php

namespace pjdietz\RestCms\Lists;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\TempTable\ArticleTempTable;

class TagNameList
{
    public static function init($options = null)
    {
        if ($options === null) {
            $options = array();
        }

        $tmpArticleJoin = '';
        $tmpArticle = new ArticleTempTable($options);
        if ($tmpArticle->isRequired()) {
            $tmpArticleJoin .= <<<QUERY
JOIN articleTag at
    ON t.tagId = at.tagId
JOIN article a
    ON at.articleId = a.articleId
JOIN tmpArticleId
    ON a.articleId = tmpArticleId.articleId
QUERY;
        }

        $query = <<<QUERY
SELECT
    t.tagName
FROM
    tag t
{$tmpArticleJoin}
ORDER BY
    t.tagName;
QUERY;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $tmpArticle->drop();

        return $collection;
    }
}
