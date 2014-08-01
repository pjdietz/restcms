<?php

namespace pjdietz\RestCms\Exceptions;

use Exception;
use JsonSchema\Validator;

class JsonException extends ResourceException
{
    private $validator;
    private $schema;

    public function __construct(
        $message = "",
        $code = 0,
        Exception $previous = null,
        Validator &$validator = null,
        $schema = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validator = $validator;
        $this->schema = $schema;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return null
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
