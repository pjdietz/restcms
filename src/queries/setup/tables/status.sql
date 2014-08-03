CREATE TABLE IF NOT EXISTS status (
    statusId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT 'Short name for the status',
    name VARCHAR(255) NOT NULL COMMENT 'Status of the article.',
    UNIQUE INDEX idxStatusSlug (slug)
)
ENGINE = InnoDB;
