<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\JsonException;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\TextProcessors\SubArticle;

class CustomFieldModel extends RestCmsBaseModel
{
    const PATH_TO_SCHEMA = '/schema/customField.json';
    const SELECT_COLLECTION_QUERY = <<<SQL
SELECT
    cf.customFieldId,
    cf.name,
    cf.value as originalValue,
    cf.articleId,
    cf.sortOrder
FROM
    customField cf
WHERE
    cf.articleId = :articleId
ORDER BY
    cf.sortOrder,
    cf.name;
SQL;
    const SELECT_ITEM_QUERY = <<<SQL
SELECT
    cf.customFieldId,
    cf.name,
    cf.value as originalValue,
    cf.articleId,
    cf.sortOrder
FROM
    customField cf
WHERE
    cf.articleId = :articleId
    AND cf.customFieldId = :customFieldId
LIMIT 1;
SQL;
    const INSERT_QUERY = <<<SQL
INSERT INTO customField (
    dateCreated,
    dateModified,
    name,
    value,
    articleId,
    sortOrder
) VALUES (
    NOW(),
    NOW(),
    :name,
    :originalValue,
    :articleId,
    :sortOrder
);
SQL;
    const UPDATE_QUERY = <<<SQL
UPDATE customField
SET
    dateModified = NOW(),
    name = :name,
    value = :originalValue,
    sortOrder = :sortOrder
WHERE
    articleId = :articleId
    AND customFieldId = :customFieldId;
SQL;
    const DELETE_QUERY = <<<SQL
DELETE FROM customField
WHERE
    articleId = :articleId
    AND customFieldId = :customFieldId;
SQL;

    public $customFieldId;
    public $name;
    public $value;
    public $originalValue;
    public $articleId;
    public $sortOrder = 0;

    /**
     * @param int $articleId
     * @param int $customFieldId
     * @throws ResourceException
     * @return CustomFieldModel
     */
    public static function init($articleId, $customFieldId)
    {
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare(self::SELECT_ITEM_QUERY);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':customFieldId', $customFieldId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException(sprintf(
                    'No custom field with ID %d for article %d',
                    $customFieldId,
                    $articleId
                ),
                ResourceException::NOT_FOUND
            );
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param int $articleId
     * @return array  Array of CustomFieldModel instances.
     */
    public static function initCollection($articleId)
    {
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare(self::SELECT_COLLECTION_QUERY);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * Read and validate a JSON representation into the data member.
     *
     * Returns the parsed data, if valid. Otherwise, returns null.
     *
     * @param string $jsonString
     * @throws JsonException
     * @return ArticleModel
     */
    public static function initWithJson($jsonString)
    {
        if (self::validateJson($jsonString, $validator) === false) {
            throw new JsonException('Unable to decode article', null, null, $validator, self::PATH_TO_SCHEMA);
        }
        return new self(json_decode($jsonString));
    }

    /**
     * Validate the passed JSON string against the class's schema.
     *
     * @param string $json
     * @param object $validator  JsonSchema validator reference
     * @return bool
     */
    private static function validateJson($json, &$validator)
    {
        $schema = file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::PATH_TO_SCHEMA);

        $validator = new Validator();
        $validator->check(json_decode($json), json_decode($schema));

        return $validator->isValid();
    }

    public function create($articleId = 0)
    {
        if ($articleId !== 0) {
            $this->articleId = $articleId;
        }
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $stmt = $db->prepare(self::INSERT_QUERY);
        }
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':originalValue', $this->originalValue, PDO::PARAM_STR);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':sortOrder', $this->sortOrder, PDO::PARAM_INT);
        $stmt->execute();
        $this->customFieldId = (int) $db->lastInsertId();
    }

    public function update()
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $stmt = $db->prepare(self::UPDATE_QUERY);
        }
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':originalValue', $this->originalValue, PDO::PARAM_STR);
        $stmt->bindValue(':sortOrder', $this->sortOrder, PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':customFieldId', $this->customFieldId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete()
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $stmt = $db->prepare(self::DELETE_QUERY);
        }
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':customFieldId', $this->customFieldId, PDO::PARAM_INT);
        $stmt->execute();
    }

    protected function prepareInstance()
    {
        $this->customFieldId = (int) $this->customFieldId;
        $this->articleId = (int) $this->articleId;
        $this->sortOrder = (int) $this->sortOrder;
        $this->processValue();
    }

    private function processValue()
    {
        if (!isset($this->originalValue)) {
            return;
        }

        $value = $this->originalValue;

        // Replace references to other articles with actual article content.
        $processor = new SubArticle();
        $value = $processor->transform($value);

        // Update the instance member.
        $this->value = $value;
    }
}
