<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;

class TagTempTable extends TempTableBase
{
    private $tagId;
    private $tagName;

    public function isRequired()
    {
        return (bool) ($this->tagId || $this->tagName);
    }

    protected function readOptions(array $options)
    {
        $this->tagId = array();
        $this->tagName = array();

        if (isset($options['tag']) && $options['tag'] !== '') {
            $ids = explode(',', $options['tag']);
            foreach ($ids as $id) {
                if (is_numeric($id)) {
                    $this->tagId[] = (int) $id;
                } else {
                    $this->tagName[] = $id;
                }
            }
        }
    }

    protected function getDropQuery()
    {
        return 'DROP TEMPORARY TABLE IF EXISTS tmpTag;';
    }

    protected function getCreateQuery()
    {
        return <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS tmpTag (
    articleId TINYINT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpTagArticleId(articleId)
);
SQL;
    }

    protected function populate(PDO $db)
    {
        if ($this->tagId) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpTag
SELECT
    at.articleId
FROM articleTag at
    JOIN tag t
        ON at.tagId = t.tagId
WHERE tagId = :id;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->tagId as $id) {
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

        }
        if ($this->tagName) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpTag
SELECT
    at.articleId
FROM articleTag at
    JOIN tag t
        ON at.tagId = t.tagId
WHERE tagName = :tagName;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->tagName as $name) {
                $stmt->bindValue(':tagName', $name, PDO::PARAM_STR);
                $stmt->execute();
            }

        }
    }
}
