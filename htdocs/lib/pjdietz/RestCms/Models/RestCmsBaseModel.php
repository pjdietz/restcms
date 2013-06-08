<?php

namespace pjdietz\RestCms\Models;

use stdClass;

/**
 * Base class for models. Models are inted for use with PDO.
 */
abstract class RestCmsBaseModel extends stdClass
{
    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}