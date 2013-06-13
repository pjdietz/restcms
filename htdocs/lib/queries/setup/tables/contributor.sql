CREATE TABLE IF NOT EXISTS contributor (
    contributorId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    articleId INT UNSIGNED NOT NULL,
    userId  INT UNSIGNED NOT NULL,
    UNIQUE INDEX idxContributor (userId, articleId)
)
ENGINE = MyISAM;
