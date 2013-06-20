<?php

namespace pjdietz\RestCms;

interface RestCmsCommonInterface
{
    const GROUP_ADMIN = 1;
    const GROUP_CONTRIBUTOR = 2;
    const GROUP_CONSUMER = 3;

    const PRIV_READ_ARTICLE = 1;
    const PRIV_CREATE_ARTICLE = 2;
    const PRIV_MODIFY_ARTICLE = 3;
    const PRIV_MODIFY_ANY_ARTICLE = 4;

    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_PENDING = 3;
    const STATUS_REMOVED = 4;
}