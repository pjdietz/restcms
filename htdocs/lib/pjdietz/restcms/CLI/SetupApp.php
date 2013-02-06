<?php

namespace pjdietz\restcms\CLI;

use pjdietz\CliApp\CliApp;
use pjdietz\CliApp\CLIAppException;
use pjdietz\restcms\Connections\Database;

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
        $this->buildSqlTables();
        return 0;
    }

    protected function help()
    {
        $this->messageWrite("This is the help message.\n");
    }

    protected function buildSqlTables()
    {
        $db = Database::getDatabaseConnection();

        // TODO Create database if needed.
        // TODO Drop database if requested.
        // TODO Drop tables if requested.
        // TODO Merge custom names into queries.

        $queries = array(
            'setup/tables/article',
            'setup/tables/articleVersion',
            'setup/tables/user',
            'setup/default-data'
        );

        $this->message("Building tables...\n");

        foreach ($queries as $query) {
            $db->exec(Database::getQuery($query));
            $this->message("  " . $query . "\n", self::VERBOSITY_VERBOSE);
        }

    }

}
