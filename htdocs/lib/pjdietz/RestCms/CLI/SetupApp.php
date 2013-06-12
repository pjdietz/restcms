<?php

namespace pjdietz\RestCms\CLI;

use pjdietz\CliApp\CliApp;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\config;

class SetupApp extends CliApp
{
    public function __construct()
    {
        parent::__construct();
        $this->optsShort = 'dhsv';
        $this->optsLong = array('debug', 'help', 'silent', 'verbose');

        // Set defaults.
        $this->verbosity_message_default = self::VERBOSITY_VERBOSE;
    }

    protected function readOpts()
    {
        // Help
        if (isset($this->options['h']) || isset($this->options['help'])) {
            $this->help();
            exit(0);
        }

        // Verbose
        if (isset($this->options['v']) || isset($this->options['verbose'])) {
            $this->verbosity = self::VERBOSITY_VERBOSE;
        }

        // Silent
        if (isset($this->options['s']) || isset($this->options['silent'])) {
            $this->verbosity = self::VERBOSITY_SILENT;
        }
    }

    protected function main()
    {
        $db = Database::getDatabaseConnection(false);
        $query = 'DROP DATABASE ' . config\MYSQL_DATABASE;
        $db->exec($query);

        $this->createDatabase();
        return 0;
    }

    protected function help()
    {
        $this->messageWrite("This is the help message.\n");
    }

    protected function createDatabase()
    {
        $db = Database::getDatabaseConnection(false);

        // Create database.
        $this->message("Creating database...");
        $query = Database::getQuery('setup/create-database');
        $db->exec($query);
        $this->message("OK\n");

        // Create tables.
        $this->message("Building tables...");

        $queryNames = array(
            'setup/tables/article',
            'setup/tables/version',
            'setup/tables/status',
            'setup/tables/user',
            'setup/tables/userGroup',
            'setup/tables/userPrivilege',
            'setup/tables/userGroupPrivilege',
            'setup/default-data'
        );

        foreach ($queryNames as $queryName) {
            $query = Database::getQuery($queryName);
            $db->exec($query);
        }

        $this->message("OK\n");
    }


}
