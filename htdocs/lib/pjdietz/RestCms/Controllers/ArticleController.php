<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;
use PDO;

abstract class ArticleController extends RestCmsBaseController
{
    /**
     * Create a temp table to filter by status.
     *
     * @param array $options
     * @return bool  Options indicate this table is required.
     */
    protected function createTmpStatus($options)
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