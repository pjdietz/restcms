CREATE TABLE IF NOT EXISTS status (
    statusId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    statusName VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Status of the article. Will be normalized to lut later.'
)
ENGINE = MyISAM;
