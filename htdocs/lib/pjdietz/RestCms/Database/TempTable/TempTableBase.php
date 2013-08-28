<?php

namespace pjdietz\RestCms\Database\TempTable;

use PDO;
use pjdietz\RestCms\Database\Database;

abstract class TempTableBase implements TempTableInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->readOptions($options);
        $this->create();
    }

    /**
     * Drop the temporary table, if it is required.
     */
    public function drop()
    {
        if ($this->isRequired()) {
            $db = Database::getDatabaseConnection();
            $db->exec($this->getDropQuery());
        }
    }

    /**
     * @return bool
     */
    abstract public function isRequired();

    /**
     * Read the options passed to the constructor and configure the instance.
     *
     * @param array $options
     */
    abstract protected function readOptions(array $options);

    /**
     * @return string
     */
    abstract protected function getDropQuery();

    /**
     * @return string
     */
    abstract protected function getCreateQuery();

    /**
     * @param PDO $databaseConnection
     */
    abstract protected function populate(PDO $databaseConnection);

    /**
     * Create the temporary table, if it is required.
     */
    private function create()
    {
        if ($this->isRequired()) {
            $db = Database::getDatabaseConnection();
            $db->exec($this->getDropQuery());
            $db->exec($this->getCreateQuery());
            $this->populate($db);
        }
    }
}
