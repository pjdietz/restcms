<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;
use PDO;

class StatusController extends RestCmsBaseController
{
    public function readCollection($options = null)
    {
        $useTmpStatus = $this->createTmpStatus($options);

        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusName as `status`
FROM status s

SQL;

        if ($useTmpStatus) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON s.statusId = ts.statusId

QUERY;
        }

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Drop temporary tables.
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $this->data;
    }

    public function readItem($options)
    {
        $useTmpStatus = $this->createTmpStatus($options);

        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusName as `status`
FROM status s

SQL;

        if ($useTmpStatus) {
            $query .= <<<'QUERY'
    JOIN tmpStatus ts
        ON s.statusId = ts.statusId

QUERY;
        }

        $query .= 'LIMIT 1';

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetch(PDO::FETCH_OBJ);

        // Drop temporary tables.
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $this->data;
    }

}