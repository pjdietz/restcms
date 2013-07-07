<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\TextProcessors\SubArticle;

class SiteModel extends RestCmsBaseModel implements RestCmsCommonInterface
{
    /**
     * @param $siteId
     * @return SiteModel
     * @throws ResourceException
     */
    static public function init($siteId)
    {
        if (is_numeric($siteId)) {
            $siteSlug = '';
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

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->bindValue(':siteSlug', $siteSlug, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return new self($stmt->fetchObject());
    }

    /**
     * @param string $path Path relative to the root of this site.
     * @return string Full URI for the given path on this site.
     */
    public function makeUri($path)
    {
        return "{$this->protocol}://{$this->hostname}{$path}";
    }

    /**
     * @param string $path
     * @throws ResourceException
     * @return ArticleModel
     */
    public function getArticleWithPath($path)
    {
        // Search the database for the articleId matching this criteria.
        $query = <<<SQL
SELECT
    a.articleId
FROM
    article a
WHERE 1 = 1
    AND a.siteId = :siteId
    AND a.sitePath = :sitePath
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':siteId', $this->siteId, PDO::PARAM_INT);
        $stmt->bindValue(':sitePath', $path, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException(
                "No resource at \"{$path}\" for site {$this->siteId}",
                ResourceException::NOT_FOUND
            );
        }

        $articleId = (int) $stmt->fetchColumn();
        return ArticleModel::init($articleId);
    }

    protected function prepareInstance()
    {
        $this->siteId = (int) $this->siteId;
    }
}