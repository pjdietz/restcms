<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use stdClass;

class CustomFieldModel extends RestCmsBaseModel
{
    public $name;
    public $value;
    public $articleId;
    public $customFieldId;
    public $customFieldValueId;

    /**
     * @param int $articleId
     * @return array List of CustomFieldModel instances assigned to the article.
     */
    public static function initCollection($articleId)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    cf.customFieldId,
    cfv.customFieldValueId,
    cf.name,
    cfv.value
FROM customFieldValue cfv
    JOIN customField cf
        ON cfv.customFieldId = cf.customFieldId
    JOIN article a
        ON cfv.articleId = a.articleId
WHERE
    a.articleId = :articleId
ORDER BY
    cf.name;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * @param $articleId
     * @return stdClass Key-value pairs assigned to the article.
     */
    public static function initObject($articleId)
    {
        $collection = self::initCollection($articleId);
        $obj = new stdClass();
        foreach ($collection as $field) {
            $obj->{$field->name} = $field->value;
        }
        return $obj;
    }

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->customFieldId = (int) $this->customFieldId;
        $this->customFieldValueId = (int) $this->customFieldValueId;
        $this->value = json_decode($this->value);
    }
}
