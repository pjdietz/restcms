<?php

namespace pjdietz\RestCms\Content;

use PDO;

class ContentReader
{
    private $modelClass;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass = "stdClass")
    {
        $this->modelClass = $modelClass;
    }

    public function readCollection(array $args, PDO $db)
    {
        $query = <<<QUERY
SELECT
    c.contentId,
    c.dateCreated,
    c.dateModified,
    c.datePublished,
    c.slug,
    c.name,
    c.path,
    c.contentType
FROM content c
QUERY;
        $where = array();

        if (isset($args["path"])) {
            $where[] = " path = :path ";
        }

        if ($where) {
            $query .= " WHERE " . join(" AND ", $where);
        }

        $stmt = $db->prepare($query);
        if (isset($args["path"])) {
            $stmt->bindValue(":path", $args["path"], PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->modelClass);
    }

}
