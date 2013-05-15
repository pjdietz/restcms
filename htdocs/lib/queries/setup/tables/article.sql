CREATE TABLE IF NOT EXISTS article (
    articleId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name for the article.',
    contentType VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Mime type for the article',
    statusId TINYINT NOT NULL DEFAULT 1 COMMENT 'Status of the article. Default is draft',
    currentArticleVersionId INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Link to articleVersion record',
    UNIQUE INDEX idxArticleSlug (slug)
)
ENGINE = MyISAM;
