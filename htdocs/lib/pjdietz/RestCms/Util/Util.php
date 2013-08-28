<?php

namespace pjdietz\RestCms\Util;

class Util
{
    /**
     * Convert a human-readable string to a boolean.
     *
     * Case insensitively treats true, t, yes, y, and 1 as true.
     * Anything else is false.
     *
     * @param string $str
     * @return bool
     */
    public static function stringToBool($str)
    {
        $str = strtolower((string) $str);

        return in_array(
            $str,
            array(
                'true',
                't',
                'yes',
                'y',
                '1'
            )
        );
    }
}
