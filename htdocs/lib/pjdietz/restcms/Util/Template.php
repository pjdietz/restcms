<?php

namespace pjdietz\restcms\Util;

Class Template
{
    protected static $fields;

    public function setField($field, $value)
    {
        if (!isset(self::$fields)) {
            self::$fields = array();
        }

        self::$fields[$field] = $value;
    }

    public function getField()
    {

    }

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
