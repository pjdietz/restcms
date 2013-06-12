<?php

namespace pjdietz\RestCms\Database\Helpers;

use PDO;
use pjdietz\RestCms\Database\Database;

class ArticleHelper extends BaseHelper
{
    private $articleId;
    private $articleSlug;

    public function __construct(array $options)
    {
        $this->articleId = array();
        $this->articleSlug = array();

        if (isset($options['article']) && $options['article'] !== '') {
            $articles = explode(',', $options['article']);
            foreach ($articles as $article) {
                if (is_numeric($article)) {
                    $this->articleId[] = (int) $article;
                } else {
                    $this->articleSlug[] = $article;
                }
            }
        }

        $this->create();
    }

    public function create()
    {
        // Return if there is no need to make the temp table.
        if (!($this->articleId || $this->articleSlug)) {
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

        if ($this->articleId) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE articleId = :articleId;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->articleId as $articleId) {
                $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
                $stmt->execute();
            }

        }

        if ($this->articleSlug) {

            // Prepare the insert statement.
            $query = <<<SQL
INSERT IGNORE INTO tmpArticleId
SELECT articleId
FROM article
WHERE slug = :slug;
SQL;
            $stmt = $db->prepare($query);

            // Execute the query for each status.
            foreach ($this->articleSlug as $slug) {
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