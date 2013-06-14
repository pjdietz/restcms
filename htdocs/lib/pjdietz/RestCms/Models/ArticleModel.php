<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\Helpers\ArticleHelper;
use pjdietz\RestCms\Database\Helpers\StatusHelper;
use pjdietz\RestCms\Exceptions\DatabaseException;
use pjdietz\RestCms\Exceptions\ResourceException;

class ArticleModel extends RestCmsBaseModel
{
    const PATH_TO_SCHEMA = '/schema/article.json';
    /** @var int */
    public $articleId;
    /** @var array List of userIds of users who may contribute to the article. */
    private $contributors;

    /**
     * Read a collection of Articles filtered by the given options array.
     *
     * @param array $options
     * @return array|null
     */
    public static function initCollection($options)
    {
        $tmpArticle = new ArticleHelper($options);
        $tmpStatus = new StatusHelper($options);

        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName AS status,
    v.title,
    v.excerpt
FROM
    article a
    JOIN version v
        ON a.currentVersionId = v.versionId
    JOIN status s
        ON a.statusId = s.statusId
SQL;

        if ($tmpArticle->isRequired()) {
            $query .= " JOIN tmpArticleId ta ON a.articleId = ta.articleId";
        }

        if ($tmpStatus->isRequired()) {
            $query .= " JOIN tmpStatus ts ON a.statusId = ts.statusId";
        }

        $query .= ";";

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());

        $tmpArticle->drop();
        $tmpStatus->drop();

        return $collection;
    }

    /**
     * Read an article from the database by articleId.
     * @param int $articleId
     * @return ArticleModel
     * @throws ResourceException
     */
    public static function initWithId($articleId)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.contentType,
    s.statusName AS status,
    v.title,
    v.content,
    v.excerpt,
    v.notes
FROM
    article a
    JOIN version v
        ON a.currentVersionId = v.versionId
        AND a.articleId = :articleId
    JOIN status s
        ON a.statusId = s.statusId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return new self($stmt->fetchObject());
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
    public static function initWithJson($jsonString, &$validator)
    {
        if (self::validateJson($jsonString, $validator) === false) {
            return null;
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

    /**
     * Assign a user as a contributor on an article.
     *
     * @param UserModel $user
     */
    public function addContributor(UserModel $user)
    {
        // Skip if this user is already a contributor.
        if ($this->hasContributor($user)) {
            return;
        }

        $query = <<<SQL
INSERT INTO contributor (
    dateCreated,
    dateModified,
    articleId,
    userId
) VALUES (
    NOW(),
    NOW(),
    :articleId,
    :userId
);
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $user->userId, PDO::PARAM_INT);
        $stmt->execute();

        $this->contributors[] = $user->userId;
    }

    public function hasContributor(UserModel $user)
    {
        return in_array($user->userId, $this->contributors);
    }

    /** Store the Article instance to the database. */
    public function create()
    {
        // Find the statusId.
        $status = StatusModel::initWithSlug($this->status);

        if (!$status) {
            throw new DatabaseException('Status ' . $this->status . ' is invalid', 400);
        }

        $statusId = $status->statusId;
        unset($status);

        // Validate all members before writing.

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
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $this->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new DatabaseException('Unable to store article. Make sure the slug is unique.', 409);
        }
        $this->articleId = (int) $db->lastInsertId();

        // Insert the version.
        $query = <<<SQL
INSERT INTO version (
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
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':parentArticleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();
        $versionId = $db->lastInsertId();

        // Set the version as current.
        $query = <<<SQL
UPDATE
    article
SET
    currentVersionId = :currentVersionId
WHERE 1 = 1
    AND articleId = :articleId;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':currentVersionId', $versionId, PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();
    }

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->readContributors();
    }

    private function readContributors()
    {
        $query = <<<SQL
SELECT c.userId
FROM contributor c
WHERE c.articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $contributors = array();
        foreach ($results as $result) {
            $contributors[] = (int) $result->userId;
        }
        $this->contributors = $contributors;
    }
}
