<?php

namespace pjdietz\RestCms\Lists;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\Helpers\SiteHelper;
use pjdietz\RestCms\Database\Helpers\StatusHelper;

class ArticleIdList
{
    public static function init($options)
    {
        $tmpSiteJoin = '';
        $tmpSite = new SiteHelper($options);
        if ($tmpSite->isRequired()) {
            $tmpSiteJoin .= <<<QUERY
JOIN site
    ON a.siteId = site.siteId
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
{$tmpSiteJoin}
{$tmpStatusJoin}
;
QUERY;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $tmpStatus->drop();

        return $collection;
    }
}
