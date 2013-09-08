<?php

namespace pjdietz\RestCms\Lists;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\TempTable\ArticleTempTable;
use pjdietz\RestCms\Database\TempTable\SitePathTempTable;
use pjdietz\RestCms\Database\TempTable\SiteTempTable;
use pjdietz\RestCms\Database\TempTable\StatusTempTable;
use pjdietz\RestCms\Database\TempTable\TagTempTable;
use pjdietz\RestCms\Util\Util;

class ArticleIdList
{
    public static function init($options)
    {
        $tmpArticleJoin = '';
        $tmpArticle = new ArticleTempTable($options);
        if ($tmpArticle->isRequired()) {
            $tmpArticleJoin .= <<<QUERY
JOIN tmpArticleId
    ON a.articleId = tmpArticleId.articleId
QUERY;
        }

        $tmpSiteJoin = '';
        $tmpSite = new SiteTempTable($options);
        if ($tmpSite->isRequired()) {
            $tmpSiteJoin .= <<<QUERY
JOIN tmpSite
    ON a.siteId = tmpSite.siteId
QUERY;
        }

        $tmpSitePathJoin = '';
        $tmpSitePath = new SitePathTempTable($options);
        if ($tmpSitePath->isRequired()) {
            $tmpSitePathJoin .= <<<QUERY
JOIN tmpSitePath
    ON a.articleId = tmpSitePath.articleId
QUERY;
        }

        $tmpStatusJoin = '';
        $tmpStatus = new StatusTempTable($options);
        if ($tmpStatus->isRequired()) {
            $tmpStatusJoin .= <<<QUERY
JOIN status s
    ON a.statusId = s.statusId
JOIN tmpStatus
    ON a.statusId = tmpStatus.statusId
QUERY;
        }

        $tmpTagJoin = '';
        $tmpTag = new TagTempTable($options);
        if ($tmpTag->isRequired()) {
            $tmpTagJoin .= <<<QUERY
JOIN tmpTag
    ON a.articleId = tmpTag.articleId
QUERY;
        }

        $wherePublic = '';
        if (isset($options['public']) && Util::stringToBool($options['public'])) {
            $wherePublic = ' AND a.public = 1';
        }

        $limit = '';
        if (isset($options['limit']) && is_numeric($options['limit'])) {
            $offset = 0;
            if (isset($options['offset']) && is_numeric($options['offset'])) {
                $offset = $options['offset'];
            }
            $limit = sprintf('LIMIT %d OFFSET %d', $options['limit'], $offset);
        }

        $query = <<<QUERY
SELECT
    a.articleId
FROM
    article a
{$tmpArticleJoin}
{$tmpSiteJoin}
{$tmpSitePathJoin}
{$tmpStatusJoin}
{$tmpTagJoin}
WHERE 1 = 1
    {$wherePublic}
ORDER BY
    a.datePublished DESC,
    a.dateModified DESC
{$limit};
QUERY;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($collection as &$item) {
            $item = (int) $item;
        }

        $tmpArticle->drop();
        $tmpSite->drop();
        $tmpStatus->drop();

        return $collection;
    }
}
