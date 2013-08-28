<?php

namespace pjdietz\RestCms\Lists;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\Helpers\ArticleHelper;
use pjdietz\RestCms\Database\Helpers\SiteHelper;
use pjdietz\RestCms\Database\Helpers\StatusHelper;

class ArticleIdList
{
    public static function init($options)
    {
        $tmpArticleJoin = '';
        $tmpArticle = new ArticleHelper($options);
        if ($tmpArticle->isRequired()) {
            $tmpArticleJoin .= <<<QUERY
JOIN tmpArticleId ta
    ON a.articleId = ta.articleId
QUERY;
        }

        $tmpSiteJoin = '';
        $tmpSite = new SiteHelper($options);
        if ($tmpSite->isRequired()) {
            $tmpSiteJoin .= <<<QUERY
JOIN tmpSite ts
    ON a.siteId = ts.siteId
QUERY;
        }

        $tmpStatusJoin = '';
        $tmpStatus = new StatusHelper($options);
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
