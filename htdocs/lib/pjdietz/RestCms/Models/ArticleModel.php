<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use PDO;
use PDOException;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\JsonException;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Lists\ArticleIdList;
use pjdietz\RestCms\Lists\TagNameList;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\RestCms\TextProcessors\SubArticle;
use pjdietz\RestCms\TextProcessors\TextProcessorInterface;
use pjdietz\RestCms\TextProcessors\TextReplacement;
use pjdietz\RestCms\Util\Util;
use RestCmsConfig\DefaultTextProcessor;

class ArticleModel extends RestCmsBaseModel implements RestCmsCommonInterface
{
    const PATH_TO_SCHEMA = '/schema/article.json';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const MAX_EXCERPT_LENGTH = 50;

    public $articleId;
    public $currentVersionId;
    public $datePublished;
    public $dateModified;
    public $status = 'draft';
    public $title;
    public $slug;
    public $autoExcerpt = '';
    public $excerpt = '';
    public $content;
    public $originalContent;
    public $contentType = 'text/html';
    public $siteId;
    public $sitePath = '';
    public $notes = '';
    public $customFields;
    public $tags;
    public $processors;
    private $processorModels;

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

        $this->updateProcessorAssignments();
        $this->updateTagAssignments();
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
            "contentType",
            "datePublished",
            "excerpt",
            "notes",
            "originalContent",
            "processors",
            "siteId",
            "sitePath",
            "slug",
            "status",
            "tags",
            "title"
        );
        foreach ($properties as $property) {
            $this->$property = $update->$property;
        }
        $this->prepareInstance();
    }

    /**
     * Update the instance with properties from a partial article.
     * @param ArticlePatchModel $patch
     */
    public function patch(ArticlePatchModel $patch)
    {
        // Copy properties that may be modified by a user.
        $properties = array(
            "contentType",
            "datePublished",
            "excerpt",
            "notes",
            "originalContent",
            "processors",
            "siteId",
            "sitePath",
            "slug",
            "status",
            "tags",
            "title"
        );
        foreach ($properties as $property) {
            if (isset($patch->{$property})) {
                $this->{$property} = $patch->{$property};
            }
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

        // Read a representation of this article in its currently stored state.
        // Compare this against the instance to see if the instance has changes.
        $current = self::init($this->articleId);

        // Insert a new version, if any version field have changed.
        if ($this->title != $current->title
            || $this->excerpt != $current->excerpt
            || $this->notes != $current->notes
            || $this->originalContent != $current->originalContent
        ) {
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
            $this->currentVersionId = $db->lastInsertId();
        }

        // Update the article record, if anything has changed.
        if ($this->contentType != $current->contentType
            || $this->currentVersionId != $current->currentVersionId
            || $this->datePublished != $current->datePublished
            || $this->slug != $current->slug
            || $this->status != $current->status
            || $this->siteId != $current->siteId
            || $this->sitePath != $current->sitePath
        ) {

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
    currentVersionId = :currentVersionId
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
            $stmt->bindValue(':currentVersionId', $this->currentVersionId, PDO::PARAM_INT);
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

        }

        $this->updateProcessorAssignments();
        $this->updateTagAssignments();
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

        // Unassign all tags.
        foreach ($this->tags as $tagName) {
            $tag = TagModel::initWithName($tagName);
            $this->unassignTag($tag);
        }
    }

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->currentVersionId = (int) $this->currentVersionId;
        $this->siteId = (int) $this->siteId;
        $this->datePublished = date(self::DATE_TIME_FORMAT, strtotime($this->datePublished));
        $this->dateModified = date(self::DATE_TIME_FORMAT, strtotime($this->dateModified));

        if (!isset($this->customFields)) {
            $this->customFields = CustomFieldModel::initCollection($this->articleId);
        }

        if (!isset($this->tags)) {
            $this->tags = TagNameList::init(array('article' => $this->articleId));
        }

        if (!isset($this->processors)) {
            if (!isset($this->processorModels)) {
                $this->processorModels = ProcessorModel::initCollectionForArticle($this->articleId);
            }
            $this->processors = array();
            foreach ($this->processorModels as $processorModel) {
                /** @var ProcessorModel $processorModel */
                $this->processors[] = $processorModel->processorName;
            }
        }

        $this->readContributors();
        $this->processContent();
    }

    private function updateProcessorAssignments()
    {
        $assignedProcessors = ProcessorModel::initCollectionForArticle($this->articleId);
        $assignedNames = array();
        foreach ($assignedProcessors as $assignedProcessor) {
            $assignedNames[] = $assignedProcessor->processorName;
        }

        // Skip if there's no change.
        if ($this->processors == $assignedNames) {
            return;
        }

        // Remove unknown processors.
        // Find processor in the instance but not in CMS.
        // Then, find processors in instance but not in list of bad processors.
        $cmsProcessors = ProcessorModel::initCollection();
        $cmsNames = array();
        foreach ($cmsProcessors as $cmsProcessor) {
            $cmsNames[] = $cmsProcessor->processorName;
        }
        $bad = array_diff($this->processors, $cmsNames);
        $this->processors = array_diff($this->processors, $bad);

        // Recheck if there's no change.
        if ($this->processors == $assignedNames) {
            return;
        }

        // Remove existing processors.
        $this->unassignProcessors();

        // Assign new processors.
        $sortOrder = 0;
        foreach ($this->processors as $processorName) {
            try {
                $processor = ProcessorModel::initWithName($processorName);
                $this->assignProcessor($processor, $sortOrder++);
            } catch (ResourceException $e) {
                error_log("Skipping invalid processor: $processorName");
            }
        }
    }

    /**
     * @param ProcessorModel $processor
     * @param int $sortOrder
     */
    private function assignProcessor(ProcessorModel $processor, $sortOrder)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
INSERT INTO articleProcessor (
    dateCreated,
    dateModified,
    articleId,
    processorId,
    sortOrder
) VALUES (
    NOW(),
    NOW(),
    :articleId,
    :processorId,
    :sortOrder
);
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':processorId', $processor->processorId, PDO::PARAM_INT);
        $stmt->bindValue(':sortOrder', $sortOrder, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Unlink all processors assigned to the instance.
     */
    private function unassignProcessors()
    {
        $db = Database::getDatabaseConnection();
        $query = <<<SQL
DELETE FROM articleProcessor
WHERE articleId = :articleId;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function updateTagAssignments()
    {
        // Find a list of all tags that exit in the CMS.
        $cmsTags =  TagNameList::init();

        // Find tags currently assigned to this article.
        $assignedTags = TagNameList::init(array('article' => $this->articleId));

        // Find tags the instance has that the current article does not.
        $toAssign = array_diff($this->tags, $assignedTags);

        // Find tags the current article has that the instance does not.
        $toRemove = array_diff($assignedTags, $this->tags);

        // Assign new tags.
        foreach ($toAssign as $tagName) {
            if (in_array($tagName, $cmsTags)) {
                $tag = TagModel::initWithName($tagName);
            } else {
                $tag = TagModel::createNewTag($tagName);
            }
            $this->assignTag($tag);
        }

        // Remove tags no longer related to the article.
        foreach ($toRemove as $tagName) {
            $tag = TagModel::initWithName($tagName);
            $this->unassignTag($tag);
        }
    }

    private function assignTag(TagModel $tag)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
INSERT INTO articleTag (
    dateCreated,
    dateModified,
    articleId,
    tagId
) VALUES (
    NOW(),
    NOW(),
    :articleId,
    :tagId
);
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':tagId', $tag->tagId, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function unassignTag(TagModel $tag)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
DELETE FROM articleTag
WHERE
    articleId = :articleId
    AND tagId = :tagId;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':articleId', $this->articleId, PDO::PARAM_INT);
        $stmt->bindValue(':tagId', $tag->tagId, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function processContent()
    {
        if (!isset($this->originalContent)) {
            return;
        }

        $maxWords = self::MAX_EXCERPT_LENGTH;
        $words = explode(' ', $this->originalContent, $maxWords + 1);
        $words = array_slice($words, 0, $maxWords);
        $excerpt = trim(join(' ', $words));
        $content = $this->originalContent;

        foreach ($this->processorModels as $processorModel) {
            /** @var ProcessorModel $processorModel */
            $excerpt = $processorModel->process($excerpt);
            $content = $processorModel->process($content);
        }

        $this->autoExcerpt = $excerpt;
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
