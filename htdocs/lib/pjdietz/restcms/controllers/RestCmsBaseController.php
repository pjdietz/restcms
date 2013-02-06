<?php

namespace pjdietz\restcms\controllers;

use \pjdietz\restcms\config;
use PDO;

abstract class RestCmsBaseController
{

    /**
     * The instance's main data store.
     *
     * @var array
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
