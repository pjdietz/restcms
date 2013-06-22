<?php

namespace RestCmsConfig;

use pjdietz\RestCms\TextProcessors\TextProcessorInterface;
use pjdietz\RestCms\TextProcessors\MarkdownExtra;

class DefaultTextProcessor implements TextProcessorInterface
{
    public function transform($text)
    {
        $processor = new MarkdownExtra();
        $text = $processor->transform($text);

        return $text;
    }
}
