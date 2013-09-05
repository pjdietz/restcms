CREATE TABLE IF NOT EXISTS customField (
    customFieldId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    name VARCHAR(255) NOT NULL COMMENT '[Required] Key for the custom field.',
    description VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Notes describing the purpose of the field.',
    UNIQUE INDEX idxCustomFieldName (name)
)
ENGINE = MyISAM;
