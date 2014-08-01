<?php

namespace pjdietz\RestCms\Util;

Class Template
{
    /**
     * Merge an associative array into a string template.
     *
     * @param string $template
     * @param array $mergeFields
     * @return string
     */
    public static function stringFromTemplate($template, $mergeFields)
    {
        return str_replace(
            array_keys($mergeFields),
            array_values($mergeFields),
            $template);
    }
}
