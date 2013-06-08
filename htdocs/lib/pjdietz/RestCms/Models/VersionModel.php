<?php

namespace pjdietz\RestCms\Models;

class VersionModel extends RestCmsBaseModel
{
    public $articleId;
    public $versionId;
    public $isCurrent;

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
        $this->versionId = (int) $this->versionId;
        $this->isCurrent = (bool) $this->isCurrent;
    }
}
