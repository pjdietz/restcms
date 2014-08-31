CREATE TABLE IF NOT EXISTS locale (
    localeId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    slug VARCHAR(255) NOT NULL COMMENT '[Required] Unique, URL-safe name',
    name VARCHAR(255) NOT NULL COMMENT '[Required] Human readable name',
    UNIQUE INDEX idxLocaleSlug (slug)
)
ENGINE = InnoDB;
