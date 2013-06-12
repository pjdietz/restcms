<?php

namespace pjdietz\RestCms\Database\Helpers;

interface DatabaseHelperInterface
{
    /** Create the temporary table */
    public function create();
    public function drop();
    public function isRequired();
}