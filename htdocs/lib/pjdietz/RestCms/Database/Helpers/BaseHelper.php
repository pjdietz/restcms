<?php

namespace pjdietz\RestCms\Database\Helpers;

abstract class BaseHelper implements DatabaseHelperInterface
{
    protected $required = false;

    /** @return bool  The temp table made by the helper is required. */
    public function isRequired()
    {
        return $this->required;
    }
}
