<?php

namespace restcms\controllers;

class ArticleItemController extends RestCmsBaseController {

    /**
     * @param string $articleId
     * @return ArticleItemController|bool
     */
    public static function newFromArticleId($articleId) {

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
    public static function newFromSlug($slug) {

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

?>