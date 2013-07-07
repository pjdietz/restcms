CREATE TABLE IF NOT EXISTS site (
    siteId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] [Unique] URL-safe name for the article.',
    hostname VARCHAR(255) NOT NULL COMMENT '[Required] [Unique] Domain name for the website',
    protocol VARCHAR(10) NOT NULL DEFAULT 'http' COMMENT '[Required] Default protocol for URLs on this site.',
    UNIQUE INDEX idxArticleSlug (slug),
    UNIQUE INDEX idxHostname (hostname)
)
ENGINE = MyISAM;
