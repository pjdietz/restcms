CREATE TABLE IF NOT EXISTS user (
    userId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    username VARCHAR(255) NOT NULL COMMENT '[Required] [Unique] Username for the user.',
    passwordHash CHAR(64) NOT NULL COMMENT '[Required] Hashed password.',
    passwordSalt CHAR(48) NOT NULL DEFAULT '' COMMENT '48-character string used as salt to make the password hash',
    emailAddress VARCHAR(255) NOT NULL DEFAULT '' COMMENT '[Requred] [Unique] Email address for the user.',
    displayName VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'Friendly name, ex: Peter Griffin',
    isAdmin TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User has elevated privlidges.',
    UNIQUE INDEX idxUserUsername (username),
    UNIQUE INDEX idxUserEmailAddress (emailAddress),
    INDEX idxUserUsernamePasswordHash (username, passwordHash)
)
ENGINE = MyISAM;
