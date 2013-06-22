<?php

namespace pjdietz\RestCms\TextProcessors;

use pjdietz\RestCms\Util\Template;
use RestCmsConfig\GlobalTextReplacement;

class TextReplacement
{
    private $mergeFields;

    public function __construct()
    {
        $this->mergeFields = GlobalTextReplacement::getMergeFields();

        // TODO: Testing only.
        $this->mergeFields['{{REPLACE_ME}}'] = 'TextReplacement';
    }

    public function transform($text)
    {
        return Template::stringFromTemplate($text, $this->mergeFields);
    }
}