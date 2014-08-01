<?php

namespace pjdietz\RestCms\Models;

use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Models\RestCmsBaseModel;

class CustomFieldNameModel extends RestCmsBaseModel
{
    public $name;
    public $customFieldId;

    /**
     * @param int $customFieldId
     * @throws ResourceException
     * @return CustomFieldNameModel
     */
    public static function initWithId($customFieldId)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
SELECT
    cf.customFieldId,
    cf.name
FROM
    customField cf
WHERE
    cf.customFieldId = :customFieldId
LIMIT 1;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':customFieldId', $customFieldId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException("No custom field with ID $customFieldId", ResourceException::NOT_FOUND);
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param string $name
     * @return CustomFieldNameModel
     * @throws ResourceException
     */
    public static function initWithName($name)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
SELECT
    cf.customFieldId,
    cf.name
FROM
    customField cf
WHERE
    cf.name = :name
LIMIT 1;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException("No custom field with name $name", ResourceException::NOT_FOUND);
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @return array All Custom Fields
     */
    public static function initCollection()
    {
        $query = <<<SQL
SELECT
    cf.customFieldId,
    cf.name
FROM
    customField cf
ORDER BY
    cf.name;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * @param int $name
     * @throws ResourceException
     * @return CustomFieldNameModel
     */
    public static function createNewField($name)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
INSERT INTO customField (
    dateCreated,
    dateModified,
    name
) VALUES (
    NOW(),
    NOW(),
    :name
);
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new ResourceException(
                'Unable to store custom field. Make sure the name is unique.',
                ResourceException::CONFLICT
            );
        }
        $tagId = (int) $db->lastInsertId();
        return self::initWithId($tagId);
    }

    protected function prepareInstance()
    {
        $this->customFieldId = (int) $this->customFieldId;
    }
}
