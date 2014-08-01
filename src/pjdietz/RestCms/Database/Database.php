<?php

namespace pjdietz\RestCms\Database;

use PDOException;
use pjdietz\RestCms\Exceptions\SetupException;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\Util;
use PDO;
use InvalidArgumentException;
use RestCmsConfig\ConfigInterface;

class Database implements RestCmsCommonInterface, ConfigInterface
{
    /**
     * Shared PDO singleton instance.
     *
     * @var PDO
     */
    private static $databaseConnection;

    /**
     * Array of merge fields to replace into template strings.
     *
     * @var array
     */
    private static $templateMergeFields;

    /**
     * Return the PDO singleton instance, creating it if needed.
     *
     * @param bool $useDefaultDatabase
     * @throws SetupException
     * @return PDO
     */
    public static function getDatabaseConnection($useDefaultDatabase = true)
    {
        if (!isset(self::$databaseConnection)) {

            // Create a new instance of the database and store it statically.
            if ($useDefaultDatabase) {
                $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8',
                    self::MYSQL_HOSTNAME, self::MYSQL_DATABASE);
            } else {
                $dsn = sprintf('mysql:host=%s;charset=utf8', self::MYSQL_HOSTNAME);
            }

            try {
                self::$databaseConnection = new PDO($dsn, self::MYSQL_USERNAME, self::MYSQL_PASSWORD);
            } catch (PDOException $e) {
                throw new SetupException(
                    'Unable to connect to database. Ensure configuration is correct in RestCmsConfig\\ConfigInterface',
                    SetupException::DATABASE_CONNECTION
                );
            }
            self::$databaseConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$databaseConnection->exec("SET NAMES utf8;");
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
        $pathToQuery = self::QUERIES_DIR . $queryName . '.sql';
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
    private static function stringFromTemplate($template, $mergeFields = null)
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

    private static function getMergeFields()
    {
        if (!isset(self::$templateMergeFields)) {
            self::$templateMergeFields = array(
                '{DATABASE_NAME}' => self::MYSQL_DATABASE,
                '{GROUP_ADMIN}' => self::GROUP_ADMIN,
                '{GROUP_CONTRIBUTOR}' => self::GROUP_CONTRIBUTOR,
                '{GROUP_CONSUMER}' => self::GROUP_CONSUMER,
                '{PRIV_READ_ARTICLE}' => self::PRIV_READ_ARTICLE,
                '{PRIV_CREATE_ARTICLE}' => self::PRIV_CREATE_ARTICLE,
                '{PRIV_MODIFY_ARTICLE}' => self::PRIV_MODIFY_ARTICLE,
                '{PRIV_MODIFY_ANY_ARTICLE}' => self::PRIV_MODIFY_ANY_ARTICLE,
                '{STATUS_DRAFT}' => self::STATUS_DRAFT,
                '{STATUS_PUBLISHED}' => self::STATUS_PUBLISHED,
                '{STATUS_PENDING}' => self::STATUS_PENDING,
                '{STATUS_REMOVED}' => self::STATUS_REMOVED
            );
        }
        return self::$templateMergeFields;
    }

}
