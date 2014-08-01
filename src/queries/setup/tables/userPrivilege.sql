CREATE TABLE IF NOT EXISTS userPrivilege (
    userPrivilegeId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    privilegeName VARCHAR(255) NOT NULL COMMENT 'Name for the user privilege',
    UNIQUE INDEX idxUserPrivilegeName (privilegeName)
)
ENGINE = MyISAM;
