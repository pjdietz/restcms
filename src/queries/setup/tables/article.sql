CREATE TABLE IF NOT EXISTS article (
    articleId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    datePublished DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name for the article.',
    contentType VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Mime type for the article',
    statusId TINYINT NOT NULL DEFAULT 1 COMMENT 'Status of the article. Default is draft',
    currentVersionId INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Link to version record',
    siteId INT UNSIGNED COMMENT '[Optional] Site the article belongs to',
    sitePath VARCHAR(255) COMMENT '[Optional] Published path on a given site for this article',
    public TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Article should be made discoverable (RSS, search, etc.)',
    UNIQUE INDEX idxArticleSlug (slug),
    UNIQUE INDEX idxSitePath (siteId, sitePath)
)
ENGINE = InnoDB;
