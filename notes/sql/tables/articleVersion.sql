CREATE TABLE IF NOT EXISTS articleVersion (
	articleVersionId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
	dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    parentArticleId INT UNSIGNED NOT NULL COMMENT '[Required] Link to article header record',
    content TEXT NOT NULL DEFAULT '' COMMENT 'Full content of the article',
    contentSearchable TEXT NOT NULL DEFAULT '' COMMENT 'Searchable content of the article; text only  with HTML tags stripped',
    notes VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Internal use; notes about the article.',
    FULLTEXT(contentSearchable)
)
ENGINE = MyISAM;
