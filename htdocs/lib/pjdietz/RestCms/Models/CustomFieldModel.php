<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\TextProcessors\SubArticle;

class CustomFieldModel extends RestCmsBaseModel
{
    const SELECT_COLLECTION_QUERY = <<<SQL
SELECT
    cf.customFieldId,
    cf.name,
    cf.value as originalValue,
    cf.articleId
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
    cf.articleId
FROM
    customField cf
WHERE
    cf.articleId = :articleId
LIMIT 1;
SQL;

    public $customFieldId;
    public $name;
    public $value;
    public $originalValue;
    public $articleId;

    /**
     * @param ArticleModel $article
     * @param int $customFieldId
     * @throws ResourceException
     * @return CustomFieldModel
     */
    public static function init(ArticleModel $article, $customFieldId)
    {
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare(self::SELECT_COLLECTION_QUERY);
        $stmt->bindValue(':articleId', $article->articleId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException(sprintf(
                    'Custom field with ID %d for article %d',
                    $customFieldId,
                    $article->articleId
                ),
                ResourceException::NOT_FOUND
            );
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param ArticleModel $article
     * @return array  Array of CustomFieldModel instances.
     */
    public static function initCollection(ArticleModel $article)
    {
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare(self::SELECT_COLLECTION_QUERY);
        $stmt->bindValue(':articleId', $article->articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    protected function prepareInstance()
    {
        $this->customFieldId = (int) $this->customFieldId;
        $this->articleId = (int) $this->articleId;
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
