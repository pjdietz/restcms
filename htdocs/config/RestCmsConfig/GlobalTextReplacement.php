<?php

namespace RestCmsConfig;

class GlobalTextReplacement
{
    /**
     * @return array
     */
    public static function getMergeFields()
    {
        return array(
            '{{NAME}}' => 'Molly'
        );
    }
}
