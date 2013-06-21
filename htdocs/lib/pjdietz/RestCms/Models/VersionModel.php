<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;

class VersionModel extends RestCmsBaseModel
{
    const VERSION_MODEL = 'pjdietz\RestCms\Models\VersionModel';
    /** @var int */
    public $articleId;
    /** @var int */
    public $versionId;
    /** @var bool */
    public $isCurrent;

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->versionId = (int) $this->versionId;
        $this->isCurrent = (bool) $this->isCurrent;
    }

    /**
     * Read the version for the given article and version Ids.
     *
     * @param int $articleId
     * @param int $versionId
     * @return VersionModel|null
     */
    public static function init($articleId, $versionId)
    {
        $query = <<<SQL
SELECT
    v.versionId,
    v.dateCreated,
    v.title,
    v.content,
    v.excerpt,
    v.notes,
    a.articleId,
    IF (a.currentVersionId = v.versionId, 1, 0) AS `isCurrent`
FROM
    article a
    JOIN version v
        ON a.articleId = v.parentArticleId
        AND a.articleId = :articleId
WHERE 1 = 1
    AND v.versionId = :versionId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':versionId', $versionId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() !== 1) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return new self($stmt->fetchObject());
    }

    /**
     * Read the collection of version for the given article.
     *
     * @param int $articleId
     * @return array|null
     */
    public static function initCollection($articleId)
    {
        $query = <<<SQL
SELECT
    v.versionId,
    v.dateCreated,
    a.articleId,
    IF (a.currentVersionId = v.versionId, 1, 0) AS `isCurrent`
FROM
    article a
    JOIN version v
        ON a.articleId = v.parentArticleId
        AND a.articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::VERSION_MODEL);
    }
}
