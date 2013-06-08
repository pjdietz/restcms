<?php

namespace pjdietz\RestCms\Models;

class VersionModel extends RestCmsBaseModel
{
    public $articleId;
    public $articleVersionId;

    private function __construct()
    {
        $this->articleId = (int) $this->articleId;
        $this->articleVersionId = (int) $this->articleVersionId;
    }
}
