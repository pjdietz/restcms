<?php

namespace pjdietz\RestCms\Controllers;

use JsonSchema\Validator;
use pjdietz\RestCms\Connections\Database;
use PDO;

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
