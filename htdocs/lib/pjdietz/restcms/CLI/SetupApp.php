<?php

namespace pjdietz\restcms\CLI;

use pjdietz\CliApp\CliApp;
use pjdietz\CliApp\CLIAppException;

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
        $this->message("Display only when verbose.\n", self::VERBOSITY_VERBOSE);
        $this->message("Display when running normally.\n", self::VERBOSITY_NORMAL);
        $this->message("Display even when silent.\n", self::VERBOSITY_SILENT);
        return 0;
    }

    protected function help()
    {
        $this->messageWrite("This is the help message.\n");
    }

}
