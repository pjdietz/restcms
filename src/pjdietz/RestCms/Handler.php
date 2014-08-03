<?php

namespace pjdietz\RestCms;

/**
 * Base class for RestCms handlers. This class extracts the configuration, if present.
 */
abstract class Handler extends \pjdietz\WellRESTed\Handler
{
    /** @var Configuration */
    protected $configuration;

    protected function buildResponse()
    {
        if (isset($this->args["configuration"])) {
            $this->configuration = $this->args["configuration"];
        }
        parent::buildResponse();
    }
}
