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
}
