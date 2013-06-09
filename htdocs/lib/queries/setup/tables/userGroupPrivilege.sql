CREATE TABLE IF NOT EXISTS userGroupPrivilege (
    userGroupPrivilegeId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    userGroupId INT UNSIGNED NOT NULL,
    userPrivilegeId INT UNSIGNED NOT NULL,
    UNIQUE INDEX userGroupPrivilege (userGroupId, userPrivilegeId)
)
ENGINE = MyISAM;
