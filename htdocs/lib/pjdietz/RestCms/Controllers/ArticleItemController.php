<?php

namespace pjdietz\RestCms\Controllers;

class ArticleItemController extends RestCmsBaseController
{

    /**
     * Validate and construct a new instance from a JSON string.
     *
     * @param string $jsonString
     * @param \JsonSchema\Validator $validator
     * @return ArticleItemController|null
     */
    public static function newFromJson($jsonString, &$validator)
    {
        $schema = $_SERVER['DOCUMENT_ROOT'] . '/schema/article.json';
        $schema = file_get_contents($schema);

        $jsonData = json_decode($jsonString);

        $validator = new \JsonSchema\Validator();
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
     * @return ArticleItemController|bool
     */
    public static function newFromArticleId($articleId)
    {

        $query = "
SELECT
    articleId,
	dateCreated,
	dateModified,
    slug,
    title
FROM
    article
WHERE 1 = 1
    AND articleId=?
ORDER BY
    dateCreated
LIMIT 1;";

        $db = self::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows && count($rows) === 1) {
            $klass = __CLASS__;
            $article = new $klass();
            $article->data = $rows[0];
            return $article;
        } else {
            return false;
        }

    }

    /**
     * @param string $slug
     * @return ArticleItemController|bool
     */
    public static function newFromSlug($slug)
    {

        $query = "
SELECT
    articleId,
	dateCreated,
	dateModified,
    slug,
    title
FROM
    article
WHERE 1 = 1
    AND slug=?
ORDER BY
    dateCreated
LIMIT 1;";

        $db = self::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $slug, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows && count($rows) === 1) {
            $klass = __CLASS__;
            $article = new $klass();
            $article->data = $rows[0];
            return $article;
        } else {
            return false;
        }

    }

}
