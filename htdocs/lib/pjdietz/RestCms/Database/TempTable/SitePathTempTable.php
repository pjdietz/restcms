<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;

class SitePathTempTable extends TempTableBase
{
    private $sitePathPattern;

    public function isRequired()
    {
        return isset($this->sitePathPattern);
    }

    protected function readOptions(array $options)
    {
        if (isset($options['sitePath']) && $options['sitePath'] !== '') {
            $this->sitePathPattern = $options['sitePath'];
        }
    }

    protected function getDropQuery()
    {
        return 'DROP TEMPORARY TABLE IF EXISTS tmpSitePath;';
    }

    protected function getCreateQuery()
    {
        return <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS tmpSitePath (
    articleId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpSitePathArticleId(articleId)
);
SQL;
    }

    protected function populate(PDO $db)
    {
        if (isset($this->sitePathPattern)) {
            $query = <<<SQL
INSERT IGNORE INTO tmpSitePath
SELECT articleId
FROM article
WHERE sitePath REGEXP :sitePathPattern;
SQL;
            $stmt = $db->prepare($query);
            $stmt->bindValue(':sitePathPattern', $this->sitePathPattern, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
