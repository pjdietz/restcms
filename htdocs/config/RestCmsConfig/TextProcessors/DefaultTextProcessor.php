<?php

namespace RestCmsConfig\TextProcessors;

use pjdietz\RestCms\TextProcessors\TextProcessorInterface;

class DefaultTextProcessor implements TextProcessorInterface
{
    public function transform($text)
    {
        $processorClasses = array(
            'pjdietz\\RestCms\\TextProcessors\\SubArticle',
            'pjdietz\\RestCms\\TextProcessors\\MarkdownExtra'
        );

        foreach ($processorClasses as $processorClass) {
            /** @var TextProcessorInterface $processor  */
            $processor = new $processorClass();
            $text = $processor->transform($text);
        }

        return $text;
    }
}
