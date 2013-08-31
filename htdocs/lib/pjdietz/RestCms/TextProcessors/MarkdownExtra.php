<?php

namespace pjdietz\RestCms\TextProcessors;

use dflydev\markdown\MarkdownExtraParser;

class MarkdownExtra implements TextProcessorInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function process($text)
    {
        $markdownParser = new MarkdownExtraParser();
        return $markdownParser->transformMarkdown($text);
    }
}
