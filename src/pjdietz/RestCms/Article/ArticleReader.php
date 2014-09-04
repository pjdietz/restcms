<?php

namespace pjdietz\RestCms\Article;

use PDO;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

/**
 * Reads Contents stored in the CMS.
 */
class ArticleReader
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
     * Read a content from the database by ID or slug.
     *
     * @param PDO $db Database connection
     * @param int|string $id contentId or slug for the resource
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return mixed
     */
    public function read(PDO $db, $id)
    {
        if (is_numeric($id)) {
            return $this->readWithId($db, $id);
        }
        return $this->readWithSlug($db, $id);
    }

    /**
     * Read a content from the database by ID.
     *
     * @param PDO $db Database connection
     * @param int $id contentId or slug for the resource
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return mixed
     */
    public function readWithId(PDO $db, $id)
    {
        $query = $this->getQueryStem();
        $query .= <<<QUERY
WHERE
    a.articleId = :articleId
LIMIT 1;
QUERY;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject($this->modelClass);
    }

    /**
     * Read a content from the database by Slug.
     *
     * @param PDO $db Database connection
     * @param string $slug contentId or slug for the resource
     * @throws \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     * @return mixed
     */
    public function readWithSlug(PDO $db, $slug)
    {
        $query = $this->getQueryStem();
        $query .= <<<QUERY
WHERE
    a.slug = :slug
LIMIT 1;
QUERY;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject($this->modelClass);
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

        $query = $this->getQueryStem();

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
            $where[] = " a.localeId = 0 ";
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

    private function getQueryStem()
    {
        return <<<QUERY
SELECT
    a.articleId,
    a.dateCreated,
    a.dateModified,
    a.datePublished,
    a.slug,
    a.name,
    a.path,
    a.contentType,
    l.slug AS locale,
    v.content
FROM article a
    JOIN version v
        ON a.versionId = v.versionId
    LEFT JOIN locale l
        ON a.localeId = l.localeId

QUERY;
    }

}
