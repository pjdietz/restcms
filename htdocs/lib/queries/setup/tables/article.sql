CREATE TABLE IF NOT EXISTS article (
	articleId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
	dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name for the article.',
    contentType VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Mime type for the article',
    status VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Status of the article. Will be normalized to lut later.',
    currentArticleVersionId INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Link to articleVersion record',
    UNIQUE INDEX idxArticleSlug (slug)
)
ENGINE = MyISAM;
