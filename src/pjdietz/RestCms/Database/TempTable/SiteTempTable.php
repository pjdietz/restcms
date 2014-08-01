<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;

class SiteTempTable extends TempTableBase
{
    private $id;
    private $slug;

    public function isRequired()
    {
        return (bool) ($this->id || $this->slug);
    }

    protected function readOptions(array $options)
    {
        $this->id = array();
        $this->slug = array();

        if (isset($options['site']) && $options['site'] !== '') {
            $ids = explode(',', $options['site']);
            foreach ($ids as $id) {
                if (is_numeric($id)) {
                    $this->id[] = (int) $id;
                } else {
                    $this->slug[] = $id;
                }
            }
        }
    }

    protected function getDropQuery()
    {
        return 'DROP TEMPORARY TABLE IF EXISTS tmpSite;';
    }

    protected function getCreateQuery()
    {
        return <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS tmpSite (
    siteId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpSiteSiteId(siteId)
);
SQL;
    }

    protected function populate(PDO $db)
    {
        if ($this->id) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpSite
SELECT siteId
FROM site
WHERE siteId = :id;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->id as $id) {
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

        }
        if ($this->slug) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpSite
SELECT siteId
FROM site
WHERE slug = :slug;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->slug as $slug) {
                $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
                $stmt->execute();
            }

        }
    }
}
