<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;

class StatusTempTable extends TempTableBase
{
    private $id;
    private $slug;

    public function isRequired()
    {
        return (bool) ($this->id || $this->slug);
    }

    protected function readOptions(array $options)
    {
        $this->id = array();
        $this->slug = array();

        if (isset($options['status']) && $options['status'] !== '') {
            $ids = explode(',', $options['status']);
            foreach ($ids as $id) {
                if (is_numeric($id)) {
                    $this->id[] = (int) $id;
                } else {
                    $this->slug[] = $id;
                }
            }
        }
    }

    protected function getDropQuery()
    {
        return 'DROP TEMPORARY TABLE IF EXISTS tmpStatus;';
    }

    protected function getCreateQuery()
    {
        return <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS tmpStatus (
    statusId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpStatusStatusId(statusId)
);
SQL;
    }

    protected function populate(PDO $db)
    {
        if ($this->id) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusId = :id;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->id as $id) {
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

        }

        if ($this->slug) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpStatus
SELECT statusId
FROM status
WHERE statusSlug = :slug;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->slug as $slug) {
                $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
                $stmt->execute();
            }

        }
    }
}
