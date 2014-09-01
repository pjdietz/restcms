CREATE TABLE IF NOT EXISTS content (
    contentId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    datePublished DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name',
    name VARCHAR(255) NOT NULL COMMENT '[Required] Human readable name',
    path VARCHAR(255) COMMENT '[Optional] Uri path component for the content',
    contentType VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Mime type',
    versionId INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reference to verion.versionId',
    localeId INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reference to locale.localeId',
    UNIQUE INDEX idxContentSlug (slug, localeId),
    UNIQUE INDEX idxContentPath (path, localeId)
)
ENGINE = InnoDB;
