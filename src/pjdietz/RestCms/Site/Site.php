<?php

namespace pjdietz\RestCms\Site;

use PDO;
use pjdietz\RestCms\Model;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

class Site extends Model
{
    /** @var PDO $db Database connection */
    private $db;

    /**
     * @param int|string $siteId
     * @param PDO $db Database connection
     * @return Site
     * @throws NotFoundException
     */
    static public function init($siteId, PDO $db)
    {
        if (is_numeric($siteId)) {
            $siteSlug = "";
        } else {
            $siteSlug = $siteId;
            $siteId = 0;
        }

        $query = <<<SQL
SELECT
    site.siteId,
    site.slug,
    site.hostname,
    site.protocol
FROM
    site
WHERE 1 = 1
    AND (site.siteId = :siteId OR site.slug = :siteSlug)
LIMIT 1;
SQL;

        $stmt = $db->prepare($query);
        $stmt->bindValue(":siteId", $siteId, PDO::PARAM_INT);
        $stmt->bindValue(":siteSlug", $siteSlug, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }

        $site = $stmt->fetchObject(get_called_class());
        $site->db = $db;
        return $site;
    }

    /**
     * @param string $path Path relative to the root of this site.
     * @return string Full URI for the given path on this site.
     */
    public function makeUri($path)
    {
        return "{$this->protocol}://{$this->hostname}{$path}";
    }

    protected function prepareInstance()
    {
        $this->siteId = (int) $this->siteId;
    }
}
