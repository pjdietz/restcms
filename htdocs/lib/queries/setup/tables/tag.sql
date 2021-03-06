CREATE TABLE IF NOT EXISTS tag (
    tagId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    tagName VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name for the tag.',
    displayName VARCHAR(255) NOT NULL COMMENT 'Formatted name for the tag.',
    UNIQUE INDEX idxTagName (tagName)
)
ENGINE = MyISAM;
