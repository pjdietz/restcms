<?php

namespace pjdietz\RestCms\Test;

use PDO;

class MyGuestbookTest extends DatabaseTestCase
{
    public function testFakeTest()
    {
        $query = <<<SQL
SELECT * FROM guestbook;
SQL;
        $db = $this->getConnection()->getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();

        $this->assertEquals(2, $stmt->rowCount());
    }
}
