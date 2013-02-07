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
        $db = Database::getDatabaseConnection();

        $query = "
SELECT
    articleId,
	dateCreated,
	dateModified,
    slug,
    title
FROM
    article
ORDER BY
    dateCreated;";

        $stmt = $db->query($query);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->data = $rows;

    }

}
