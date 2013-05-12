<?php

namespace pjdietz\RestCms\Controllers;

use pjdietz\RestCms\config;
use pjdietz\RestCms\Connections\Database;

/**
 * Controller base class for all REST CMS controllers.
 *
 * @property mixed $data
 */
abstract class RestCmsBaseController
{

    /**
     * The instance's main data store.
     *
     * @var mixed
     */
    protected $data;

    // -------------------------------------------------------------------------
    // Accessors

    /**
     * @param string $name
     * @return array|string
     * @throws \Exception
     */
    public function __get($name)
    {
        switch ($name) {
            case 'data':
                return $this->getData();
            default:
                throw new \Exception('Property ' . $name . ' does not exist.');
        }
    }

    public function getData()
    {
        return $this->data;
    }

}
