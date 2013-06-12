<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;

class UserModel extends RestCmsBaseModel
{
    public $userId;
    private $privileges;

    public static function initWithCredentials($username, $passwordHash)
    {
        $query = <<<SQL
SELECT
    u.userId,
    u.username,
    u.passwordHash,
    COALESCE(u.emailAddress, '') AS `emailAddress`,
    u.displayName,
    u.userGroupId,
    ug.groupName AS `group`
FROM user u
    JOIN userGroup ug
        ON u.userGroupId = ug.userGroupId
        AND u.username = :username
        AND u.passwordHash = :passwordHash
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':passwordHash', $passwordHash, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $results = $stmt->fetchObject();
        return new self($results);
    }

    /**
     * Return is the user has a given privilege.
     *
     * @param array|int $privileges
     * @return bool
     */
    public function hasPrivileges($privileges)
    {
        if (is_array($privileges)) {
            foreach ($privileges as $privilegeId) {
                if (!in_array($privilegeId, $this->privileges)) {
                    return false;
                }
            }
            return true;
        }

        return in_array($privileges, $this->privileges);
    }

    protected function prepareInstance()
    {
        $this->userId = (int) $this->userId;
        $this->userGroupId = (int) $this->userGroupId;
        $this->readPrivileges();
    }

    private function readPrivileges()
    {
        $query = <<<SQL
SELECT ugp.userPrivilegeId
FROM userGroupPrivilege ugp
WHERE userGroupId = :userGroupId;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue('userGroupId', $this->userGroupId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $privs = array();
        foreach ($results as $result) {
            $privs[] = (int) $result->userPrivilegeId;
        }
        $this->privileges = $privs;
    }

}