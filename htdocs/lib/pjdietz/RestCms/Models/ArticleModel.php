<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Database\Helpers\ArticleHelper;
use pjdietz\RestCms\Database\Helpers\StatusHelper;
use pjdietz\RestCms\Exceptions\JsonException;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\TextProcessors\Markdown;

class ArticleModel extends RestCmsBaseModel implements RestCmsCommonInterface
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
    s.statusSlug AS status,
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
    s.statusSlug AS status,
    a.contentType,
    v.title,
    v.content AS originalContent,
    v.excerpt,
    v.notes,
    a.currentVersionId
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
     * @throws JsonException
     * @return ArticleModel
     */
    public static function initWithJson($jsonString, &$validator)
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

        // Add the userId to the list of contributors.
        $this->contributors[] = $user->userId;
    }

    public function hasContributor(UserModel $user)
    {
        return in_array($user->userId, $this->contributors);
    }

    public function removeContributor(UserModel $user)
    {
        // Skip if this user is not currently a contributor.
        if (!$this->hasContributor($user)) {
            return;
        }

        $query = <<<SQL
DELETE FROM contributor
WHERE 1 = 1
    AND articleId = :articleId
    AND userId = :userId
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $user->userId, PDO::PARAM_INT);
        $stmt->execute();

        // Remove the userId from the list of contributors.
        if (($key = array_search($user->userId, $this->contributors)) !== false) {
            unset($this->contributors[$key]);
        }
    }

    public function setCurrentVersion(VersionModel $version)
    {
        if ($version->articleId !== $this->articleId) {
            throw new ResourceException(
                'Version does not belong to this article.',
                ResourceException::INVALID_DATA
            );
        }

        // Update the article record to point to the new version.
        $query = <<<SQL
UPDATE
    article
SET
    dateModified = NOW(),
    currentVersionId = :newVersionId
WHERE
    articleId = :articleId;
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':newVersionId', $version->versionId, PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();

        // Re-read the article after changing the version.
        $newArticle = self::initWithId($this->articleId);
        $this->copyMembers($newArticle);
        $this->prepareInstance();
    }

    /** Store the Article instance to the database. */
    public function create()
    {
        // Find the statusId.
        try {
            $status = StatusModel::initWithSlug($this->status);
        } catch (ResourceException $e) {
            if ($e->getCode() === ResourceException::NOT_FOUND) {
                throw new ResourceException(
                    $e->getMessage(),
                    ResourceException::INVALID_DATA
                );
            }
            throw $e;
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
            throw new ResourceException(
                'Unable to store article. Make sure the slug is unique.',
                ResourceException::CONFLICT
            );
        }
        $this->articleId = (int) $db->lastInsertId();

        // Insert the version.
        $query = <<<SQL
INSERT INTO version (
    dateCreated,
    dateModified,
    title,
    parentArticleId,
    content,
    excerpt,
    notes
) VALUES (
    NOW(),
    NOW(),
    :title,
    :parentArticleId,
    :content,
    :excerpt,
    :notes
);
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':parentArticleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':content', $this->originalContent, PDO::PARAM_INT);
        $stmt->bindValue(':excerpt', $this->excerpt, PDO::PARAM_INT);
        $stmt->bindValue(':notes', $this->notes, PDO::PARAM_INT);
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

    /**
     * Update the instance with properties from another article.
     * @param ArticleModel $update
     */
    public function updateFrom(ArticleModel $update)
    {
        // Copy properties that may be modified by a user.
        $properties = array(
            "contentType",
            "status",
            "slug",
            "title",
            "originalContent",
        );
        foreach ($properties as $property) {
            $this->$property = $update->$property;
        }
    }

    /** Update the database with the instance's current state. */
    public function update()
    {
        // TODO: Validate that instance contains all required fields.

        // Find the statusId.
        try {
            $status = StatusModel::initWithSlug($this->status);
        } catch (ResourceException $e) {
            if ($e->getCode() === ResourceException::NOT_FOUND) {
                throw new ResourceException(
                    $e->getMessage(),
                    ResourceException::INVALID_DATA
                );
            }
            throw $e;
        }

        $statusId = $status->statusId;
        unset($status);

        // Insert a new version containing the current fields.
        $query = <<<SQL
INSERT INTO version (
    dateCreated,
    dateModified,
    parentArticleId,
    title,
    excerpt,
    content,
    notes
) VALUES (
    NOW(),
    NOW(),
    :articleId,
    :title,
    :excerpt,
    :content,
    :notes
);
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':excerpt', $this->excerpt, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->originalContent, PDO::PARAM_STR);
        $stmt->bindValue(':notes', $this->notes, PDO::PARAM_STR);
        $stmt->execute();
        $versionId = $db->lastInsertId();

        // Update the article record to track the new version and match the new fields.
        $query = <<<SQL
UPDATE article
SET
    dateModified = NOW(),
    slug = :slug,
    contentType = :contentType,
    statusId = :statusId,
    currentVersionId = :versionId
WHERE
    articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $this->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->bindValue(':versionId', $versionId, PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            // todo: Check if this really is the problem.
            throw new ResourceException(
                'Unable to store article. Please check the slug is not already in use.',
                ResourceException::CONFLICT
            );
        }

        $this->currentVersionId = $versionId;
    }

    /** Remove the records from the database relating to the instance. */
    public function delete()
    {
        // Mark the status for the article as Removed.
        // Also, obsufcate the slug, so that it won't collide if the user tried to re-use it.
        $query = <<<SQL
UPDATE
    article
SET
    statusId = :statusId,
    slug = :slug
WHERE
    articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':statusId', self::STATUS_REMOVED, PDO::PARAM_INT);
        $stmt->bindValue(':slug', $this->slug . '-' . SHA1(time()), PDO::PARAM_INT);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();
    }

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->readContributors();
        $this->processContent();

        if (!isset($this->excerpt)) {
            $this->excerpt = '';
        }

        if (!isset($this->notes)) {
            $this->notes = '';
        }
    }

    private function processContent()
    {
        if (!isset($this->originalContent)) {
            $this->originalContent = '';
            $this->content = '';
            return;
        }

        $content = $this->originalContent;

        $processor = new Markdown();
        $content = $processor->transform($content);

        $this->content = $content;
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
