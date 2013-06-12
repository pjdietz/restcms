<?php

namespace pjdietz\RestCms\Database\Helpers;

use PDO;
use pjdietz\RestCms\Database\Database;

class StatusHelper extends BaseHelper
{
    private $statusId;
    private $statusSlug;

    public function __construct(array $options)
    {
        $this->statusId = array();
        $this->statusSlug = array();

        if (isset($options['status']) && $options['status'] !== '') {
            $statuses = explode(',', $options['status']);
            foreach ($statuses as $status) {
                if (is_numeric($status)) {
                    $this->statusId[] = $status;
                } else {
                    $this->statusSlug[] = $status;
                }
            }
        }

        $this->create();
    }

    public function create()
    {
        // Return if there is no need to make the temp table.
        if (!($this->statusId || $this->statusSlug)) {
            return;
        }

        $db = Database::getDatabaseConnection();

        // Create an empty temp table.
        $query = <<<SQL
DROP TEMPORARY TABLE IF EXISTS tmpStatus;

CREATE TEMPORARY TABLE IF NOT EXISTS tmpStatus (
    statusId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpStatusStatusId(statusId)
);
SQL;
        $db->exec($query);


        if ($this->statusSlug) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusSlug = :statusSlug;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->statusSlug as $statusSlug) {
                $stmt->bindValue(':statusSlug', $statusSlug, PDO::PARAM_STR);
                $stmt->execute();
            }

        }

        if ($this->statusId) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusId = :statusId;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->statusId as $statusId) {
                $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
                $stmt->execute();
            }

        }

        $this->required = true;
    }

    public function drop()
    {
        if ($this->required) {
            $query = 'DROP TEMPORARY TABLE IF EXISTS tmpStatus;';
            Database::getDatabaseConnection()->exec($query);
        }
    }

}