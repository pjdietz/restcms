<?php

namespace pjdietz\restcms\Connections;

use pjdietz\restcms\Util;
use pjdietz\restcms\config;
use PDO;
use InvalidArgumentException;

class Database
{
    /**
     * Shared PDO singleton instance.
     *
     * @var \PDO
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
     * @return \PDO
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
            self::$databaseConnection->setAttribute( PDO::ATTR_EMULATE_PREPARES, false);

        }

        return self::$databaseConnection;
    }

    public static function getQuery($query)
    {
        // Read the query.
        $pathToQuery = config\QUERIES_DIR . $query . '.sql';
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
                '{DATABASE_NAME}' => config\MYSQL_DATABASE
            );
        }
        return self::$templateMergeFields;
    }

}
