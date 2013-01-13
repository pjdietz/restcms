<?php

namespace pjdietz\restcms\controllers;

class UserController extends RestCmsBaseController
{

    public static function newFromUsername($username)
    {

        $db = self::getDatabaseConnection();

        $stmt = $db->prepare("SELECT * FROM user WHERE username=? LIMIT 1");
        $stmt->execute(array($username));
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) === 1) {

            $klass = __CLASS__;
            $user = new $klass();
            $user->data = $rows[0];
            return $user;

        } else {
            return false;
        }

    }

}
