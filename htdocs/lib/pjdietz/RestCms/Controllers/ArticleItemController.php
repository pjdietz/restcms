<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use pjdietz\RestCms\Connections\Database;
use PDO;

class ArticleItemController extends ArticleController
{
    /**
     * Create a new controller and read the article from the database
     * indentified by the on the options array.
     *
     * @param array $options
     * @return ArticleItemController|null
     */
    public static function newFromOptions($options)
    {
        $controller = new ArticleItemController();
        $controller->readFromDatabase($options);
        return $controller;
    }

    /**
     * Create a new controller and read the article from the database
     * indentified by articleId
     *
     * @param string $articleId
     * @return ArticleItemController
     */
    public static function newFromArticleId($articleId)
    {
        $options = array(
            'articleId' => $articleId
        );
        return self::newFromOptions($options);
    }

    /**
     * Create a new controller and read the article from the database
     * indentified by slug
     *
     * @param string $slug
     * @return ArticleItemController
     */
    public static function newFromSlug($slug)
    {
        $options = array(
            'slug' => $slug
        );
        return self::newFromOptions($options);
    }

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

    private function readFromDatabase($options)
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
    }

}
