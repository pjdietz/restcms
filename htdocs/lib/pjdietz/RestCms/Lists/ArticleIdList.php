<?php

namespace pjdietz\RestCms\Lists;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\TempTable\ArticleTempTable;
use pjdietz\RestCms\Database\TempTable\SiteTempTable;
use pjdietz\RestCms\Database\TempTable\StatusTempTable;

class ArticleIdList
{
    public static function init($options)
    {
        $tmpArticleJoin = '';
        $tmpArticle = new ArticleTempTable($options);
        if ($tmpArticle->isRequired()) {
            $tmpArticleJoin .= <<<QUERY
JOIN tmpArticleId ta
    ON a.articleId = ta.articleId
QUERY;
        }

        $tmpSiteJoin = '';
        $tmpSite = new SiteTempTable($options);
        if ($tmpSite->isRequired()) {
            $tmpSiteJoin .= <<<QUERY
JOIN tmpSite ts
    ON a.siteId = ts.siteId
QUERY;
        }

        $tmpStatusJoin = '';
        $tmpStatus = new StatusTempTable($options);
        if ($tmpStatus->isRequired()) {
            $tmpStatusJoin .= <<<QUERY
JOIN status s
    ON a.statusId = s.statusId
JOIN tmpStatus ts
    ON a.statusId = ts.statusId
QUERY;
        }

        $query = <<<QUERY
SELECT
    a.articleId
FROM
    article a
{$tmpArticleJoin}
{$tmpSiteJoin}
{$tmpStatusJoin}
;
QUERY;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $tmpArticle->drop();
        $tmpSite->drop();
        $tmpStatus->drop();

        return $collection;
    }
}
