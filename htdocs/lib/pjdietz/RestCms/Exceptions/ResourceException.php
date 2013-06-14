<?php

namespace pjdietz\RestCms\Exceptions;

class ResourceException extends RestCmsException
{
    const NOT_FOUND = 1;
    const CONFLICT = 2;
    const INVALID_DATA = 3;
}
