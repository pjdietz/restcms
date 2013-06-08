<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\config;
use pjdietz\RestCms\Connections\Database;
use PDO;

/**
 * Controller base class for all REST CMS controllers.
 *
 * @property mixed $data
 */
abstract class RestCmsBaseController
{

    /**
     * The instance's main data store.
     *
     * @var mixed
     */
    protected $data;

    // -------------------------------------------------------------------------
    // Accessors

    /**
     * @param string $name
     * @return array|string
     * @throws \Exception
     */
    public function __get($name)
    {
        switch ($name) {
            case 'data':
                return $this->getData();
            default:
                throw new \Exception('Property ' . $name . ' does not exist.');
        }
    }

    public function getData()
    {
        return $this->data;
    }

    // -------------------------------------------------------------------------
    // Temp Table Helpers

    /**
     * Create a temp table to filter by articleId or slug
     *
     * @param array $options
     * @return bool  Options indicate this table is required.
     */
    protected function createTmpArticleId($options)
    {
        // Return false if there is no need to make the temp table.
        if (!isset($options['articleId']) && !isset($options['slug'])) {
            return false;
        }

        $db = Database::getDatabaseConnection();

        // Create an empty temp table.
        $query = <<<'SQL'
DROP TEMPORARY TABLE IF EXISTS tmpArticleId;

CREATE TEMPORARY TABLE IF NOT EXISTS tmpArticleId (
    articleId INT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpArticleId(articleId)
);
SQL;
        $db->exec($query);

        // Add by articleId

        // Prepare the insert statement.
        $query = <<<'SQL'
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE articleId = :articleId;
SQL;
        $stmt = $db->prepare($query);

        // Execute the query for each articleId.
        $articleIds = explode(',', $options['articleId']);
        $articleIds = array_filter($articleIds, 'is_numeric');

        foreach ($articleIds as $articleId) {
            $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Add by slug

        // Prepare the insert statement.
        $query = <<<'SQL'
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE slug = :slug;
SQL;
        $stmt = $db->prepare($query);

        // Execute the query for each articleId.
        $articleIds = explode(',', $options['slug']);

        foreach ($articleIds as $articleId) {
            $stmt->bindValue(':slug', $articleId, PDO::PARAM_STR);
            $stmt->execute();
        }

        return true;

    }
    protected function dropTmpArticleId()
    {
        $query = 'DROP TEMPORARY TABLE IF EXISTS tmpArticleId;';
        Database::getDatabaseConnection()->exec($query);
    }

    /**
     * Create a temp table to filter by status.
     *
     * @param array $options
     * @return bool  Options indicate this table is required.
     */
    protected function createTmpStatus($options)
    {
        $status = (isset($options['status']) && $options['status'] !== '');
        $statusId = (isset($options['statusId']) && $options['statusId'] !== '');

        // Return false if there is no need to make the temp table.
        if (!($status || $statusId)) {
            return false;
        }

        $db = Database::getDatabaseConnection();

        // Create an empty temp table.
        $query = <<<'SQL'
DROP TEMPORARY TABLE IF EXISTS tmpStatus;

CREATE TEMPORARY TABLE IF NOT EXISTS tmpStatus (
    statusId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpStatusStatusId(statusId)
);
SQL;
        $db->exec($query);

        // Prepare the insert statement.
        $query = <<<'SQL'
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusSlug = :statusSlug;
SQL;
        $stmt = $db->prepare($query);

        // Execute the query for each status.
        $statusSlugs = explode(',', $options['status']);

        foreach ($statusSlugs as $statusSlug) {
            $stmt->bindValue(':statusSlug', $statusSlug, PDO::PARAM_STR);
            $stmt->execute();
        }


        // Prepare the insert statement.
        $query = <<<'SQL'
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusId = :statusId;
SQL;
        $stmt = $db->prepare($query);

        // Execute the query for each status.
        $statusIds = explode(',', $options['statusId']);

        foreach ($statusIds as $statusId) {
            $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
            $stmt->execute();
        }

        return true;
    }
    protected function dropTmpStatus()
    {
        $query = 'DROP TEMPORARY TABLE IF EXISTS tmpStatus;';
        Database::getDatabaseConnection()->exec($query);
    }

}
