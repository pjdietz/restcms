CREATE TABLE IF NOT EXISTS processor (
    processorId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateCreated DATETIME NOT NULL DEFAULT '0000-00-00',
    dateModified DATETIME NOT NULL DEFAULT '0000-00-00',
    processorName VARCHAR(255) NOT NULL COMMENT '[Required] [Unique] Friendly name for the processor.',
    description VARCHAR(255) NOT NULL DEFAULT '',
    className VARCHAR(255) NOT NULL COMMENT '[Required] Fully qualified class name of the processor.',
    UNIQUE INDEX idxProcessorName (processorName)
)
ENGINE = MyISAM;
