<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;

class ArticleTempTable extends TempTableBase
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

        if (isset($options['article']) && $options['article'] !== '') {
            $ids = explode(',', $options['article']);
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
        return 'DROP TEMPORARY TABLE IF EXISTS tmpArticleId;';
    }

    protected function getCreateQuery()
    {
        return <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS tmpArticleId (
    articleId INT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpArticleId(articleId)
);
SQL;
    }

    protected function populate(PDO $db)
    {
        if ($this->id) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE articleId = :id;
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
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE slug = :slug;
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
