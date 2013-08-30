<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\JsonException;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Lists\ArticleIdList;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\TextProcessors\SubArticle;
use pjdietz\RestCms\TextProcessors\TextReplacement;
use pjdietz\RestCms\Util\Util;
use RestCmsConfig\DefaultTextProcessor;

class ArticleModel extends RestCmsBaseModel implements RestCmsCommonInterface
{
    const PATH_TO_SCHEMA = '/schema/article.json';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public $articleId;
    public $currentVersionId;
    public $datePublished;
    public $dateModified;
    public $status = 'draft';
    public $title;
    public $slug;
    public $excerpt = '';
    public $content;
    public $originalContent;
    public $contentType = 'text/html';
    public $siteId;
    public $sitePath = '';
    public $notes = '';
    public $customFields;

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
        $articleIds = ArticleIdList::init($options);
        if (isset($options['ids']) && Util::stringToBool($options['ids'])) {
            return $articleIds;
        }

        // Return an empty set if no IDs match.
        if (count($articleIds) === 0) {
            return array();
        }

        $articleIds = join(',', $articleIds);

        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.datePublished,
    a.dateModified,
    s.statusSlug AS status,
    a.contentType,
    a.siteId,
    a.sitePath,
    v.title,
    v.content AS originalContent,
    v.excerpt,
    v.notes,
    a.currentVersionId
FROM
    article a
    JOIN version v
        ON a.currentVersionId = v.versionId
    JOIN status s
        ON a.statusId = s.statusId
WHERE
    a.articleId IN ({$articleIds})
ORDER BY
    a.datePublished DESC,
    a.dateModified DESC
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $collection = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());

        return $collection;
    }

    /**
     * Read an article from the database by articleId or slug.
     *
     * @param int|string $articleId
     * @return ArticleModel
     * @throws ResourceException
     */
    public static function init($articleId)
    {
        if (is_numeric($articleId)) {
            return self::initWithId($articleId);
        }
        return self::initWithSlug($articleId);
    }

    /**
     * Read an article from the database by articleId.
     *
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
    a.datePublished,
    a.dateModified,
    s.statusSlug AS status,
    a.contentType,
    a.siteId,
    a.sitePath,
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

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Read an article from the database by slug.
     *
     * @param string $slug
     * @return ArticleModel
     * @throws ResourceException
     */
    public static function initWithSlug($slug)
    {
        $query = <<<SQL
SELECT
    a.articleId,
    a.slug,
    a.datePublished,
    a.dateModified,
    s.statusSlug AS status,
    a.contentType,
    a.siteId,
    a.sitePath,
    v.title,
    v.content AS originalContent,
    v.excerpt,
    v.notes,
    a.currentVersionId
FROM
    article a
    JOIN version v
        ON a.currentVersionId = v.versionId
        AND a.slug = :slug
    JOIN status s
        ON a.statusId = s.statusId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException("", ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
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
        $newArticle = self::init($this->articleId);
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
    datePublished,
    slug,
    contentType,
    statusId,
    siteId,
    sitePath
) VALUES (
    NOW(),
    NOW(),
    :datePublished,
    :slug,
    :contentType,
    :statusId,
    :siteId,
    :sitePath
);
SQL;
        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':datePublished', $this->datePublished, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $this->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->bindValue(':siteId', $this->siteId, PDO::PARAM_INT);
        $stmt->bindValue(':sitePath', $this->sitePath, PDO::PARAM_STR);
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

        // If the instance has custom fields, add them.
        if ($this->customFields) {
            foreach ($this->customFields as $customField) {
                if (!($customField instanceof CustomFieldModel)) {
                    $customField = CustomFieldModel::initWithObject($customField);
                }
                $customField->create($this->articleId);
            }
        }
    }

    /**
     * Set the original content and re-process the content.
     *
     * @param string $content New original content
     */
    public function setContent($content)
    {
        $this->originalContent = $content;
        $this->processContent();
    }

    /**
     * Update the instance with properties from another article.
     * @param ArticleModel $update
     */
    public function updateFrom(ArticleModel $update)
    {
        // Copy properties that may be modified by a user.
        $properties = array(
            "datePublished",
            "contentType",
            "status",
            "slug",
            "title",
            "originalContent",
            "siteId",
            "sitePath"
        );
        foreach ($properties as $property) {
            $this->$property = $update->$property;
        }
        $this->prepareInstance();
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
    datePublished = :datePublished,
    slug = :slug,
    contentType = :contentType,
    statusId = :statusId,
    siteId = :siteId,
    sitePath = :sitePath,
    currentVersionId = :versionId
WHERE
    articleId = :articleId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':datePublished', $this->datePublished, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':contentType', $this->contentType, PDO::PARAM_STR);
        $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->bindValue(':siteId', $this->siteId, PDO::PARAM_INT);
        $stmt->bindValue(':sitePath', $this->sitePath, PDO::PARAM_STR);
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
        $this->currentVersionId = (int) $this->currentVersionId;
        $this->siteId = (int) $this->siteId;
        $this->datePublished = date(self::DATE_TIME_FORMAT, strtotime($this->datePublished));
        $this->dateModified = date(self::DATE_TIME_FORMAT, strtotime($this->dateModified));
        $this->customFields = CustomFieldModel::initCollection($this->articleId);
        $this->readContributors();
        $this->processContent();
    }

    private function processContent()
    {
        if (!isset($this->originalContent)) {
            return;
        }

        $content = $this->originalContent;

        // Replace references to other articles with actual article content.
        $processor = new SubArticle();
        $content = $processor->transform($content);

        // TODO: add merge data for meta-data for the article (date, author, etc.)

        // Replace merge fields in the article with their values.
        $processor = new TextReplacement();
        $content = $processor->transform($content);

        // Use any user-defined text replacements.
        $processor = new DefaultTextProcessor();
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
