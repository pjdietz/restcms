<?php

namespace pjdietz\RestCms\Models;

use JsonSchema\Validator;
use pjdietz\RestCms\Exceptions\JsonException;

class ArticlePatchModel extends RestCmsBaseModel
{
    const PATH_TO_SCHEMA = '/schema/articlePatch.json';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Read and validate a JSON representation into the data member.
     *
     * Returns the parsed data, if valid. Otherwise, returns null.
     *
     * @param string $jsonString
     * @throws JsonException
     * @return ArticlePatchModel
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

    protected function prepareInstance()
    {
        if (isset($this->datePublished)) {
            $this->datePublished = date(self::DATE_TIME_FORMAT, strtotime($this->datePublished));
        }
    }
}
