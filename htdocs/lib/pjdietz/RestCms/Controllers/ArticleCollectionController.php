<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\Connections\Database;

class ArticleCollectionController extends RestCmsBaseController
{
    public function __construct($options = null)
    {
        $this->readFromDatabase();
    }

    protected function readFromDatabase()
    {
        $stmt = Database::getStatement(Database::QUERY_SELECT_ARTICLES_COLLECTION);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $this->data = $rows;
    }
}
