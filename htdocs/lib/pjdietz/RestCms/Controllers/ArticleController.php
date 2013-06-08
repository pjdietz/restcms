<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Connections\Database;
use pjdietz\RestCms\Exceptions\DatabaseException;

/**
 * Class for reading and writing Articles and performing database interactions.
 */
class ArticleController extends RestCmsBaseController
{
    const PATH_TO_SCHEMA = '/schema/article.json';
    const ARTICLE_MODEL = 'pjdietz\RestCms\Models\ArticleModel';

    /**
     * Read a collection of Articles filtered by the given options array.
     *
     * @param array $options
     * @return array|null
     */
    public function readCollection($options)
    {
        $useTmpArticleId = $this->createTmpArticleId($options);
        $useTmpStatus = $this->createTmpStatus($options);

        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName AS status,
    av.title,
    av.excerpt
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
    JOIN status s
        ON a.statusId = s.statusId
SQL;

        if ($useTmpArticleId) {
            $query .= " JOIN tmpArticleId ta ON a.articleId = ta.articleId";
        }

        if ($useTmpStatus) {
            $query .= " JOIN tmpStatus ts ON a.statusId = ts.statusId";
        }

        $query .= ";";

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_CLASS, self::ARTICLE_MODEL);

        // Drop temporary tables.
        if ($useTmpArticleId) {
            $this->dropTmpArticleId();
        }
        if ($useTmpStatus) {
            $this->dropTmpStatus();
        }

        return $collection;
    }

    /**
     * Read an article from the database identified by the articleId
     *
     * @param int $articleId
     * @return object|null
     */
    public function readItem($articleId)
    {
        $query = <<<SQL
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
        AND a.articleId = :articleId
    JOIN status s
        ON a.statusId = s.statusId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject(self::ARTICLE_MODEL);
    }

    /**
     * Read and validate a JSON representation into the data member.
     *
     * Returns the parsed data, if valid. Otherwise, returns null.
     *
     * @param string $jsonString
     * @param Validator $validator
     * @return object|null
     */
    public function parseJson($jsonString, &$validator)
    {
        if (self::validateJson($jsonString, $validator) === false) {
            return null;
        }
        return json_decode($jsonString);
    }

    /**
     * @param object $article
     * @throws DatabaseException
     * @return mixed
     */
    public function create($article)
    {
        // Find the statusId.
        $statusCtrl = new StatusController();
        $status = $statusCtrl->readItem(array('status' => $article->status));

        if (!$status) {
            throw new DatabaseException('Status ' . $article->status . ' is invalid', 400);
        }

        $statusId = $status->statusId;
        unset($status);

        // TODO Throw exceptions on slug collision or other problem writing.

        // Insert the article.
        $query = <<<SQL
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
        $stmt->bindValue(':slug', $article->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $article->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new DatabaseException('Unable to store article. Make sure the slug is unique.', 409);
        }
        $articleId = $db->lastInsertId();

        // Insert the articleVersion.
        $query = <<<SQL
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
        $stmt->bindValue(':title', $article->title, PDO::PARAM_STR);
        $stmt->bindValue(':parentArticleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $versionId = $db->lastInsertId();

        // Set the version as current.
        $query = <<<SQL
UPDATE
    article
SET
    currentArticleVersionId = :currentArticleVersionId
WHERE 1 = 1
    AND articleId = :articleId;

SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':currentArticleVersionId', $versionId,  PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->readItem($articleId);
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
