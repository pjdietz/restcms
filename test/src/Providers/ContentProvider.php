<?php

namespace pjdietz\RestCms\Test\Providers;

class ContentProvider
{
    public function validIdProvider()
    {
        return [
            [1, "cats"],
            [4, "dogs"]
        ];
    }

    public function invalidIdProvider()
    {
        return [
            [400], ["birds"]
        ];
    }

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
            ["/cats", "", null, "Cats"],
            ["/cats", "es", "es", "Gatos"],
            ["/cats", "fr,de,es", "de", "Katzen"],
            ["/dogs", "fr,de", null, "Dogs"],
        ];
    }
}
