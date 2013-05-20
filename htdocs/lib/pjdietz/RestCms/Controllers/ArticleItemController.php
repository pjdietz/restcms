<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Connections\Database;
use pjdietz\RestCms\Exceptions\DatabaseException;

class ArticleItemController extends RestCmsBaseController
{
    const PATH_TO_SCHEMA = '/schema/article.json';

    /**
     * Read and validate a JSON representation into the data member.
     *
     * Returns the parsed data, if valid. Otherwise, returns null.
     *
     * @param string $jsonString
     * @param Validator $validator
     * @return object|null
     */
    public function readFromJson($jsonString, &$validator)
    {
        if (self::validateJson($jsonString, $validator) === false) {
            $this->data = null;
            return null;
        }
        $this->data = json_decode($jsonString);
        return $this->data;
    }

    /**
     * Read the article from the database indentified by the options array.
     *
     * @param array $options
     * @return object|null
     */
    public function readFromOptions($options)
    {
        $useTmpArticleId = $this->createTmpArticleId($options);
        if ($useTmpArticleId === false) {
            return null;
        }

        $query = <<<'SQL'
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName as status,
    av.title,
    av.content,
    av.excerpt,
    av.notes
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
    JOIN status s
        ON a.statusId = s.statusId
    JOIN tmpArticleId ta
        ON a.articleId = ta.articleId
LIMIT 1;

SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Drop temporary tables.
        $this->dropTmpArticleId();

        return $this->data;
    }

    public function insert()
    {
        // Find the statusId.
        $statusCtrl = new StatusController();
        $status = $statusCtrl->readItem(array('status' => $this->data->status));

        if (!$status) {
            throw new DatabaseException('Status ' . $this->data->status . ' is invalid', 400);
        }

        $statusId = $status->statusId;
        unset($status);

        // TODO Throw exceptions on slug collision or other problem writing.

        // Insert the article.
        $query = <<<'SQL'
INSERT INTO article (
    dateCreated,
    dateModified,
    slug,
    contentType,
    statusId
) VALUES (
    NOW(),
    NOW(),
    :slug,
    :contentType,
    :statusId
);

SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $this->data->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $this->data->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new DatabaseException('Unable to store article. Make sure the slug is unique.', 409);
        }
        $articleId = $db->lastInsertId();

        // Insert the articleVersion.
        $query = <<<'SQL'
INSERT INTO articleVersion (
    dateCreated,
    dateModified,
    title,
    parentArticleId
) VALUES (
    NOW(),
    NOW(),
    :title,
    :parentArticleId
);

SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':title', $this->data->title, PDO::PARAM_STR);
        $stmt->bindValue(':parentArticleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $articleVersionid = $db->lastInsertId();

        // Set the version as current.
        $query = <<<'SQL'
UPDATE
    article
SET
    currentArticleVersionId = :currentArticleVersionId
WHERE 1 = 1
    AND articleId = :articleId;

SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':currentArticleVersionId', $articleVersionid,  PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->readFromOptions(array('articleId' => $articleId));
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

}
