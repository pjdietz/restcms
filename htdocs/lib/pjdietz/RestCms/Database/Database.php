<?php

namespace pjdietz\RestCms\Database;

use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\Util;
use pjdietz\RestCms\config;
use PDO;
use InvalidArgumentException;

class Database implements RestCmsCommonInterface
{
    /**
     * Shared PDO singleton instance.
     *
     * @var PDO
     */
    protected static $databaseConnection;

    /**
     * Array of merge fields to replace into template strings.
     *
     * @var array
     */
    protected static $templateMergeFields;

    /**
     * Return the PDO singleton instance, creating it if needed.
     *
     * @param bool $useDefaultDatabase
     * @return PDO
     */
    public static function getDatabaseConnection($useDefaultDatabase = true)
    {
        if (!isset(self::$databaseConnection)) {

            // Create a new instance of the database and store it statically.
            if ($useDefaultDatabase) {
                $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', config\MYSQL_HOSTNAME, config\MYSQL_DATABASE);
            } else {
                $dsn = sprintf('mysql:host=%s;charset=utf8', config\MYSQL_HOSTNAME);
            }

            self::$databaseConnection = new PDO($dsn, config\MYSQL_USERNAME, config\MYSQL_PASSWORD);
            self::$databaseConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$databaseConnection;
    }

    /**
     * Return a prepared statement for the query stored in the queries directory
     *
     * @param string @queryName
     * @throws \InvalidArgumentException
     * @return \PDOStatement
     */
    public static function getStatement($queryName) {
        $db = Database::getDatabaseConnection();
        $query = Database::getQuery($queryName);
        return $db->prepare($query);
    }

    /**
     * Return the query from the query directory with the given name
     *
     * @param string $queryName
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function getQuery($queryName)
    {
        // Read the query.
        $pathToQuery = config\QUERIES_DIR . $queryName . '.sql';
        if (!file_exists($pathToQuery)) {
            throw new InvalidArgumentException('file does not exist: ' . $pathToQuery);
        }
        $query = file_get_contents($pathToQuery);

        // Merge fields into the query.
        $query = self::stringFromTemplate($query);

        return $query;
    }

    /**
     * Merge an associative array into a string template.
     *
     * @param string $template
     * @param array $mergeFields
     * @return string
     */
    protected static function stringFromTemplate($template, $mergeFields = null)
    {
        if (!is_null($mergeFields)) {
            $mergeFields = array_merge(self::getMergeFields(), $mergeFields);
        } else {
            $mergeFields = self::getMergeFields();
        }

        return str_replace(
            array_keys($mergeFields),
            array_values($mergeFields),
            $template);
    }

    protected static function getMergeFields()
    {
        if (!isset(self::$templateMergeFields)) {
            self::$templateMergeFields = array(
                '{DATABASE_NAME}' => config\MYSQL_DATABASE,
                '{PRIV_READ_ARTICLE}' => self::PRIV_READ_ARTICLE,
                '{PRIV_CREATE_ARTICLE}' => self::PRIV_CREATE_ARTICLE
            );
        }
        return self::$templateMergeFields;
    }

}
