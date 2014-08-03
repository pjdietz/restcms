<?php

namespace pjdietz\RestCms\Test\Providers;

class StatusProvider
{
    public function validStatusProvider()
    {
        return [
            [1, "draft"],
            [2, "published"],
            [3, "pending"],
            [4, "removed"]
        ];
    }

    public function invalidStatusProvider()
    {
        return [
            [6], ["birds"]
        ];
    }
}
