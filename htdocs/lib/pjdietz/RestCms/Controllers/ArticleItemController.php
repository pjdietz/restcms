<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use pjdietz\RestCms\Connections\Database;
use PDO;

class ArticleItemController extends ArticleController
{
    /**
     * Validate and construct a new instance from a JSON string.
     *
     * @param string $jsonString
     * @param Validator $validator
     * @return ArticleItemController
     */
    public static function newFromJson($jsonString, &$validator)
    {
        $schema = $_SERVER['DOCUMENT_ROOT'] . '/schema/article.json';
        $schema = file_get_contents($schema);

        $jsonData = json_decode($jsonString);

        $validator = new Validator();
        $validator->check($jsonData, json_decode($schema));

        if ($validator->isValid()) {

            // Passed JSON is valid.
            // Create and return the instance.
            $klass = __CLASS__;
            $article = new $klass();
            $article->data = $jsonData;
            return $article;

        } else {

            // JSON failed validation.
            return null;

        }
    }

    /**
     * Read the article from the database indentified by the options array.
     *
     * @param array $options
     * @return object|null
     */
    public function readFromOptions($options)
    {
        $useTmpArticleId = $this->createTmpArticleId($options);
        if ($useTmpArticleId === false) {
            return null;
        }

        $query = <<<'SQL'
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName as status,
    av.title,
    av.content,
    av.excerpt,
    av.notes
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
    JOIN status s
        ON a.statusId = s.statusId
    JOIN tmpArticleId ta
        ON a.articleId = ta.articleId
LIMIT 1;

SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Drop temporary tables.
        $this->dropTmpArticleId();

        return $this->data;
    }

}
