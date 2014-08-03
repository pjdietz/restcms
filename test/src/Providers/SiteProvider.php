<?php

namespace pjdietz\RestCms\Test\Providers;

class SiteProvider
{
    public function validSiteProvider()
    {
        return [
            [1, "cats"],
            [2, "dogs"]
        ];
    }

    public function invalidSiteProvider()
    {
        return [
            [4], ["birds"]
        ];
    }
}
