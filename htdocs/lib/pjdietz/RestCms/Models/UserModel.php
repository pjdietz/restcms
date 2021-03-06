<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Exceptions\UserException;
use pjdietz\RestCms\RestCmsCommonInterface;

class UserModel extends RestCmsBaseModel implements RestCmsCommonInterface
{
    public $userId;
    private $privileges;

    /**
     * Create a new User by reading from the database.
     *
     * @param int $userId
     * @return UserModel
     * @throws ResourceException
     */
    public static function initWithId($userId)
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
        AND u.userId = :userId
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException(
                "Unable to find User with userId {$userId}",
                ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Create a new User by reading from the database.
     *
     * @param string $username
     * @return UserModel
     * @throws ResourceException
     */
    public static function initWithUsername($username)
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
LIMIT 1;
SQL;

        $db = Database::getDatabaseConnection();
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ResourceException(
                "Unable to find User with username {$username}",
                ResourceException::NOT_FOUND);
        }

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Create a new User by reading from the database.
     *
     * @param $username
     * @param $passwordHash
     * @return mixed
     * @throws UserException
     */
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
            throw new UserException('Invalid credentials', UserException::INVALID_CREDENTIALS);
        }

        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Throw an exception if the user does not have privileges for modifying the article.
     *
     * @param ArticleModel $article
     * @throws UserException
     */
    public function assertArticleAccess(ArticleModel $article)
    {
        if (!$this->hasArticleAccess($article)) {
            throw new UserException(
                'User may not modify this article',
                UserException::NOT_ALLOWED
            );
        }
    }

    /**
     * Throw an exception if the user does not have required privilege or privileges.
     *
     * @param array|int $privilege
     * @throws UserException
     */
    public function assertPrivilege($privilege)
    {
        if (!$this->hasPrivilege($privilege)) {
            throw new UserException(
                'User does not have required privilege or privileges',
                UserException::NOT_ALLOWED
            );
        }
    }

    /**
     * Return if the user has privileges for modifying the article.
     *
     * @param ArticleModel $article
     * @return bool
     */
    public function hasArticleAccess(ArticleModel $article)
    {
        // Check if the user may modify any article.
        if ($this->hasPrivilege(self::PRIV_MODIFY_ANY_ARTICLE)) {
            return true;
        }

        // Check if the user is a contributor and is able to modify.
        if ($this->hasPrivilege(self::PRIV_MODIFY_ARTICLE) && $article->hasContributor($this)) {
            return true;
        }

        return false;
    }

    /**
     * Return if the user has a given privilege or all privileges in the passed array.
     *
     * @param array|int $privilege
     * @return bool
     */
    public function hasPrivilege($privilege)
    {
        if (is_array($privilege)) {
            foreach ($privilege as $privilegeId) {
                if (!in_array($privilegeId, $this->privileges)) {
                    return false;
                }
            }
            return true;
        }
        return in_array($privilege, $this->privileges);
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

        $privileges = array();
        foreach ($results as $result) {
            $privileges[] = (int) $result->userPrivilegeId;
        }
        $this->privileges = $privileges;
    }

}
