CREATE TABLE IF NOT EXISTS customField (
    customFieldId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    name VARCHAR(255) NOT NULL COMMENT '[Required] Key for the custom field.',
    value VARCHAR(65535) NOT NULL COMMENT '[Required] Value for the custom field.',
    articleId INT UNSIGNED NOT NULL COMMENT '[Required] Relates a field to an article.',
    sortOrder SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Arbitraty sort order for fields within an article.',
    UNIQUE INDEX idxCustomFieldArticle (articleId)
)
ENGINE = MyISAM;
