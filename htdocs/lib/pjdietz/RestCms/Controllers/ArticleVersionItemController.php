<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;

class ArticleVersionItemController extends RestCmsBaseController
{
    /**
     * @param string $articleId
     * @param string $articleVersionId
     * @return ArticleVersionCollectionController
     */
    public static function newFromArticleId($articleId, $articleVersionId)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_VERSION_ITEM_BY_ARTICLE_ID);
        $stmt->bindValue(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->bindValue(':articleVersionId', $articleVersionId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            $item = new ArticleVersionCollectionController();
            $item->data = $rows[0];
            return $item;
        } else {
            return null;
        }
    }

    /**
     * @param string $slug
     * @param string $articleVersionId
     * @return ArticleVersionCollectionController
     */
    public static function newFromSlug($slug, $articleVersionId)
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLE_VERSION_ITEM_BY_SLUG);
        $stmt->bindValue(':slug', $slug, \PDO::PARAM_STR);
        $stmt->bindValue(':articleVersionId', $articleVersionId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($rows && count($rows) > 0) {
            $item = new ArticleVersionCollectionController();
            $item->data = $rows;
            return $item;
        } else {
            return null;
        }
    }
}
