<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;
use PDO;

/**
 * Class for reading and writing Article and performing database interactions.
 */
class ArticleCollectionController extends RestCmsBaseController
{
    public function __construct($options = null)
    {
        $this->readFromDatabase($options);
    }

    /**
     * Create a collection of Articles filtered by the given options array.
     *
     * TODO Document options
     *
     * @param array $options
     * @return ArticleCollectionController|null
     */
    public static function newFromOptions($options)
    {
        $controller = new ArticleCollectionController();
        $controller->readFromDatabase($options);
        return $controller;
    }

    private function readFromDatabase($options)
    {
        $query = <<<'SQL'
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName AS status,
    av.title,
    av.excerpt
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
    JOIN status s
        ON a.statusId = s.statusId

SQL;

        if ($this->createTmpStatus($options)) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON a.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create a temp table to filter by status.
     *
     * @param array $options
     * @return bool  The main query needs to join to the temp table.
     */
    private function createTmpStatus($options)
    {
        // Return false if there is no need to make the temp table.
        if (!isset($options['status']) || $options['status'] === '') {
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
WHERE statusName = :statusName;
SQL;
        $stmt = $db->prepare($query);

        // Execute the query for each realmId.
        $statusNames = explode(',', $options['status']);

        foreach ($statusNames as $statusName) {
            $stmt->bindValue(':statusName', $statusName, PDO::PARAM_STR);
            $stmt->execute();
        }

        return true;
    }

}
