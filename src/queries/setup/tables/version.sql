CREATE TABLE IF NOT EXISTS version (
    versionId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    content TEXT COMMENT 'Full text content',
    articleId INT UNSIGNED NOT NULL,
    INDEX idxVersionContentId (articleId)
)
ENGINE = InnoDB;
