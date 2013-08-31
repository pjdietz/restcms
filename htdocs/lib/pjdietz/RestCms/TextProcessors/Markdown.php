<?php

namespace pjdietz\RestCms\TextProcessors;

use dflydev\markdown\MarkdownParser;

class Markdown implements TextProcessorInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function process($text)
    {
        $markdownParser = new MarkdownParser();
        return $markdownParser->transformMarkdown($text);
    }
}
