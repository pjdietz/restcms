<?php

namespace pjdietz\RestCms\Database\Helpers;

use PDO;
use pjdietz\RestCms\Database\Database;

class ArticleHelper extends BaseHelper
{
    private $id;
    private $slug;

    public function __construct(array $options)
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

        $this->create();
    }

    public function create()
    {
        // Return if there is no need to make the temp table.
        if (!($this->id || $this->slug)) {
            return;
        }

        $db = Database::getDatabaseConnection();

        // Create an empty temp table.
        $query = <<<SQL
DROP TEMPORARY TABLE IF EXISTS tmpArticleId;

CREATE TEMPORARY TABLE IF NOT EXISTS tmpArticleId (
    articleId INT UNSIGNED NOT NULL,
    UNIQUE INDEX uidxTmpArticleId(articleId)
);
SQL;
        $db->exec($query);

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

        $this->required = true;
    }

    public function drop()
    {
        if ($this->required) {
            $query = 'DROP TEMPORARY TABLE IF EXISTS tmpArticleId;';
            Database::getDatabaseConnection()->exec($query);
        }
    }

}
