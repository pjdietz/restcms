<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\TextProcessors\SubArticle;

class SiteModel extends RestCmsBaseModel implements RestCmsCommonInterface
{
    static public function init($siteId)
    {
        $query = <<<SQL
SELECT
    site.siteId,
    site.slug,
    site.hostname,
    site.protocol
FROM
    site
WHERE 1 = 1
    AND site.siteId = :siteId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return new self($stmt->fetchObject());
    }

    public function makeUri($path)
    {
        return "{$this->protocol}://{$this->hostname}{$path}";
    }

    protected function prepareInstance()
    {
        $this->siteId = (int) $this->siteId;
    }
}