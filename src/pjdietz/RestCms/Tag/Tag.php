<?php

namespace pjdietz\RestCms\Tag;

use PDO;
use pjdietz\RestCms\Model;
use pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException;

class Tag extends Model
{
    public $tagId;
    public $slug;
    public $name;

    /**
     * @param int $tagId
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Tag
     */
    public static function init($tagId, PDO $db)
    {
        if (is_numeric($tagId)) {
            return self::initWithId($tagId, $db);
        }
        return self::initWithName($tagId, $db);
    }

    /**
     * @param int $tagId
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Tag
     */
    public static function initWithId($tagId, PDO $db)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.slug,
    t.name
FROM
    tag t
WHERE
    t.tagId = :tagId
LIMIT 1;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * @param string $slug
     * @param PDO $db Database connection
     * @throws NotFoundException
     * @return Tag
     */
    public static function initWithName($slug, PDO $db)
    {
        $query = <<<SQL
SELECT
    t.tagId,
    t.slug,
    t.name
FROM
    tag t
WHERE
    t.slug = :slug
LIMIT 1;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException();
        }
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Allow the instance to update its members after construction or deserialization.
     * @return void
     */
    protected function prepareInstance()
    {
        $this->tagId = (int) $this->tagId;
    }
}
