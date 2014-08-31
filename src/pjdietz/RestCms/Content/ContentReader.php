<?php

namespace pjdietz\RestCms\Content;

use PDO;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

/**
 * Reads Contents stored in the CMS.
 */
class ContentReader
{
    /** @var string Fully qualified name of the class to use as the model */
    private $modelClass;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Return the best matching content given a path and optional locale
     *
     * @param PDO $db Database connection
     * @param string $path Path for the content
     * @param string $locale Locale for the content
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return object
     */
    public function readWithPath(PDO $db, $path, $locale = "")
    {
        $locales = explode(",", $locale);
        $locales = array_map("trim", $locales);
        if (count($locales) === 1 && $locales[0] === "") {
            $locales = array();
        }

        $query = <<<QUERY
SELECT
    c.contentId,
    c.dateCreated,
    c.dateModified,
    c.datePublished,
    c.slug,
    c.name,
    c.path,
    c.contentType,
    l.slug AS locale
FROM content c
    LEFT JOIN locale l
        ON c.localeId = l.localeId
QUERY;

        $where = array();
        $where[] = " path = :path ";

        $order = array();

        if ($locales) {
            // Create a temporary table or locales to sort by.
            $this->createTmpLocaleSort($db, $locales);
            $query .= " LEFT JOIN tmpLocaleSort tls ON l.slug = tls.slug";
            $order[] = "tls.sortOrder DESC";
        } else {
            // Only match records with the default locale.
            $where[] = " c.localeId = 0 ";
        }

        if ($where) {
            $query .= " WHERE " . join(" AND ", $where);
        }

        if ($order) {
            $query .= " ORDER BY " . join(",", $order);
        }

        $query .= " LIMIT 1;";

        $stmt = $db->prepare($query);
        $stmt->bindValue(":path", $path, PDO::PARAM_STR);
        $stmt->execute();
        $this->dropTmpLocaleSort($db);

        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }

        return $stmt->fetchObject($this->modelClass);
    }

    private function createTmpLocaleSort(PDO $db, $locales)
    {
        $this->dropTmpLocaleSort($db);
        $db->exec("CREATE TEMPORARY TABLE IF NOT EXISTS tmpLocaleSort (
            slug VARCHAR(255),
            sortOrder TINYINT UNSIGNED
        );");

        $query = "INSERT INTO tmpLocaleSort (slug, sortOrder) VALUES (:slug, :sortOrder);";
        $stmt = $db->prepare($query);
        $sortOrder = count($locales);
        foreach ($locales as $locale) {
            $stmt->bindValue(":slug", $locale, PDO::PARAM_STR);
            $stmt->bindValue(":sortOrder", $sortOrder--, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    private function dropTmpLocaleSort(PDO $db)
    {
        $db->exec("DROP TEMPORARY TABLE IF EXISTS tmpLocaleSort;");
    }

}
