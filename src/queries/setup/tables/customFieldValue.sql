CREATE TABLE IF NOT EXISTS customFieldValue (
    customFieldValueId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    customFieldId INT UNSIGNED NOT NULL COMMENT '[Required]',
    articleId INT UNSIGNED NOT NULL COMMENT '[Required] Relates a field to an article.',
    value VARCHAR(65535) NOT NULL COMMENT '[Required] Value for the custom field.',
    UNIQUE INDEX idxCustomFieldArticle (articleId, customFieldId)
)
ENGINE = MyISAM;
