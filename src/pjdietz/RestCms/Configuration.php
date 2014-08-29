<?php

namespace pjdietz\RestCms;

use ArrayAccess;
use BadMethodCallException;
use InvalidArgumentException;
use PDO;

class Configuration implements ArrayAccess
{
    /** @var array */
    private $properties;

    public function __construct(array $properties)
    {
        $defaults = [
            "Class::Article" => __NAMESPACE__ . "\\Article\\Article",
            "Class::ArticleReader" => __NAMESPACE__ . "\\Article\\ArticleReader"
        ];

        $this->properties = array_merge($defaults, $properties);
    }

    public function getClass($className)
    {

    }

    /**
     * @return PDO
     * @throws InvalidArgumentException
     */
    public function getDatabaseConnection()
    {

    }

    public function get($instance)
    {

    }

    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->properties[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("Array access of class " . get_class($this) . " is read-only.");
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("Array access of class " . get_class($this) . " is read-only.");
    }

}
