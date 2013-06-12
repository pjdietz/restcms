<?php

namespace pjdietz\RestCms\Exceptions;

class UserException extends RestCmsException
{
    const INVALID_CREDENTIALS = 1;
    const NOT_ALLOWED = 2;
}