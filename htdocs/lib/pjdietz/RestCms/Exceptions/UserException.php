<?php

namespace pjdietz\RestCms\Exceptions;

class UserException extends ResourceException
{
    const INVALID_CREDENTIALS = 1;
    const NOT_ALLOWED = 2;
}
