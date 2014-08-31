<?php

namespace pjdietz\RestCms\Test\Providers;

class ContentProvider
{
    public function validPathProvider()
    {
        return [
            ["/cats"],
            ["/dogs"]
        ];
    }

    public function invalidPathProvider()
    {
        return [
            ["/birds"]
        ];
    }

    public function validPathAndLocaleProvider()
    {
        return [
            ["/cats", null, null],
            ["/cats", "es", "es"],
            ["/cats", "fr,de,es", "de"],
            ["/dogs", "fr,de", null],
        ];
    }
}
