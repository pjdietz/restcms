<?php

namespace pjdietz\RestCms\TextProcessors;

interface TextProcessorInterface
{
    /**
     * @param string $text The original string
     * @return string Transformed version of the string
     */
    public function transform($text);
}
