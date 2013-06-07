<?php

namespace pjdietz\RestCms\Controllers;

use PDO;
use pjdietz\RestCms\Connections\Database;

class StatusController extends RestCmsBaseController
{
    public function readCollection($options = null)
    {
        $useTmpStatus = $this->createTmpStatus($options);

        $query = <<<'SQL'
SELECT
    s.statusId,
    s.statusName AS `status`
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
    s.statusName AS `status`
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

        if ($stmt->rowCount() === 0) {
            $this->data = null;
        } else {
            $this->data = $stmt->fetch(PDO::FETCH_OBJ);
        }

        // Drop temporary tables.
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $this->data;
    }

}