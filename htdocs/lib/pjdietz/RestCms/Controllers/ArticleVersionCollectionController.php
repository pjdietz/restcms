<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;

class ArticleVersionCollectionController extends RestCmsBaseController
{
    /**
     * @param string $articleId
     * @return ArticleVersionCollectionController
     */
    public static function newFromArticleId($articleId)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_VERSIONS_COLLECTION_BY_ARTICLE_ID);
        $stmt->bindValue(1, $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            $collection = new ArticleVersionCollectionController();
            $collection->data = $rows;
            return $collection;
        } else {
            return null;
        }
    }

    /**
     * @param string $slug
     * @return ArticleVersionCollectionController
     */
    public static function newFromSlug($slug)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_VERSIONS_COLLECTION_BY_SLUG);
        $stmt->bindValue(1, $slug, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            $collection = new ArticleVersionCollectionController();
            $collection->data = $rows;
            return $collection;
        } else {
            return null;
        }
    }
}
