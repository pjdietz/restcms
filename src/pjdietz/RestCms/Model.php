<?php

namespace pjdietz\RestCms;

use stdClass;

/**
 * Base class for models.
 */
abstract class Model extends stdClass
{
    protected function __construct($source = null)
    {
        $this->copyMembers($source);
        $this->prepareInstance();
    }

    /**
     * Copy all public fields from $source into the instance.
     * @param $source
     */
    protected function copyMembers($source)
    {
        if (is_null($source)) {
            return;
        }
        if (is_array($source)) {
            $source = (object) $source;
        }

        foreach ($source as $field => $value) {
            $this->{$field} = $value;
        }
    }

    /**
     * Allow the instance to update its members after construction or deserialization.
     * @return void
     */
    abstract protected function prepareInstance();
}
