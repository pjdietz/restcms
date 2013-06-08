<?php

namespace pjdietz\RestCms\Models;

class StatusModel extends RestCmsBaseModel
{
    public $statusId;

    protected function prepareInstance()
    {
        $this->statusId = (int) $this->statusId;
    }
}
