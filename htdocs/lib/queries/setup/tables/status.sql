CREATE TABLE IF NOT EXISTS status (
    statusId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    statusSlug VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Short name for the status',
    statusName VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Status of the article.'
)
ENGINE = MyISAM;
