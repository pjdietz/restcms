<?php

namespace pjdietz\RestCms\Test\Providers;

class TagProvider
{
    public function validTagProvider()
    {
        return [
            [1, "cats"],
            [2, "rest"]
        ];
    }

    public function invalidTagProvider()
    {
        return [
            [4], ["birds"]
        ];
    }
}
