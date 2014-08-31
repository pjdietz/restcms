<?php

namespace pjdietz\RestCms;

use PDO;
use Pimple\Container;

class Configuration extends Container
{
   public function __construct()
   {
       parent::__construct();

       // Classes
       $this["ContentHandler"] = __NAMESPACE__ . "\\Content\\ContentHandler";
       $this["ContentPathHandler"] = __NAMESPACE__ . "\\Content\\ContentPathHandler";
       $this["ContentReader"] = __NAMESPACE__ . "\\Content\\ContentReader";

       // Factories
       $this["contentReader"] = function ($c) {
           $contentReaderClass = $c["ContentReader"];
           return new $contentReaderClass("stdClass");
       };

       $this["db"] = function ($c) {
           $db = new PDO($c["DB_DSN"], $c["DB_USERNAME"], $c["DB_PASSWORD"]);
           $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           return $db;
       };
   }
}
