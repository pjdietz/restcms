<?php

namespace pjdietz\RestCms\Database\Helpers;

use PDO;
use pjdietz\RestCms\Database\Database;

class SiteHelper extends BaseHelper
{
    private $id;
    private $slug;

    public function __construct(array $options)
    {
        $this->statusId = array();
        $this->statusSlug = array();

        if (isset($options['site']) && $options['site'] !== '') {
            $sites = explode(',', $options['site']);
            foreach ($sites as $site) {
                if (is_numeric($site)) {
                    $this->id[] = $site;
                } else {
                    $this->slug[] = $site;
                }
            }
        }

        $this->create();
    }

    public function create()
    {
        // Return if there is no need to make the temp table.
        if (!($this->id || $this->slug)) {
            return;
        }

        $db = Database::getDatabaseConnection();

        // Create an empty temp table.
        $query = <<<SQL
DROP TEMPORARY TABLE IF EXISTS tmpSite;

CREATE TEMPORARY TABLE IF NOT EXISTS tmpSite (
    id TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpSiteSiteId(id)
);
SQL;
        $db->exec($query);

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

        if ($this->id) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpSite
SELECT siteId
FROM status
WHERE sideId = :id;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->id as $id) {
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

        }

        $this->required = true;
    }

    public function drop()
    {
        if ($this->required) {
            $query = 'DROP TEMPORARY TABLE IF EXISTS tmpSite;';
            Database::getDatabaseConnection()->exec($query);
        }
    }

}
