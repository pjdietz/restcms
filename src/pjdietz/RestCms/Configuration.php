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
       $this["Article"] = "\\stdClass";
       $this["ArticleByPathHandler"] = __NAMESPACE__ . "\\Article\\ArticleByPathHandler";
       $this["ArticleHandler"] = __NAMESPACE__ . "\\Article\\ArticleHandler";
       $this["ArticleRawByPathHandler"] = __NAMESPACE__ . "\\Article\\ArticleRawByPathHandler";
       $this["ArticleRawHandler"] = __NAMESPACE__ . "\\Article\\ArticleRawHandler";
       $this["ArticleReader"] = __NAMESPACE__ . "\\Article\\ArticleReader";

       // Factories
       $this["articleReader"] = function ($c) {
           $articleReaderClass = $c["ArticleReader"];
           $articleClass = $c["Article"];
           return new $articleReaderClass($articleClass);
       };

       $this["db"] = function ($c) {
           $db = new PDO($c["DB_DSN"], $c["DB_USERNAME"], $c["DB_PASSWORD"]);
           $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           return $db;
       };
   }
}
