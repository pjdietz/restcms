<?php

namespace pjdietz\RestCms\Test\Providers;

class ArticleProvider
{
    public function validIdProvider()
    {
        return [
            [1, "cats"],
            [2, "dogs"]
        ];
    }

    public function invalidIdProvider()
    {
        return [
            [4], ["birds"]
        ];
    }
}
