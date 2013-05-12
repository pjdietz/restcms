<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use pjdietz\RestCms\Connections\Database;

class ArticleItemController extends RestCmsBaseController
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
     * @param string $articleId
     * @return ArticleItemController
     */
    public static function newFromArticleId($articleId)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_ITEM_BY_ARTICLE_ID);
        $stmt->bindValue(1, $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) === 1) {
            $klass = __CLASS__;
            $article = new $klass();
            $article->data = $rows[0];
            return $article;
        } else {
            return null;
        }
    }

    /**
     * @param string $slug
     * @return ArticleItemController
     */
    public static function newFromSlug($slug)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_ITEM_BY_SLUG);
        $stmt->bindValue(1, $slug, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) === 1) {
            $klass = __CLASS__;
            $article = new $klass();
            $article->data = $rows[0];
            return $article;
        } else {
            return null;
        }
    }

}
