<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\TextProcessors\TextProcessorInterface;

class ProcessorModel extends RestCmsBaseModel
{
    const TEXT_PROCESSOR_INTERFACE_FQN = '\\pjdietz\\RestCms\\TextProcessors\\TextProcessorInterface';

    public $processorId;
    public $processorName;
    public $description;
    public $className;

    /**
     * @return ProcessorModel
     */
    public static function initCollection()
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
SELECT
    p.processorId,
    p.processorName,
    p.description,
    p.className
FROM
    processor p
ORDER BY
    p.processorName;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * @param int $articleId
     * @return ProcessorModel
     */
    public static function initCollectionForArticle($articleId)
    {
        $db = Database::getDatabaseConnection();
        static $stmt = null;
        if ($stmt === null) {
            $query = <<<SQL
SELECT
    p.processorId,
    p.processorName,
    p.description,
    p.className
FROM
    processor p
    JOIN articleProcessor ap
        on p.processorId = ap.processorId
    JOIN article a
        on ap.articleId = a.articleId
ORDER BY
    ap.sortOrder,
    p.processorName;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public function process($text)
    {
        $processorClassName = $this->className;
        print $processorClassName . "\n";
        //if (is_subclass_of($processorClassName, self::TEXT_PROCESSOR_INTERFACE_FQN)) {
            /** @var TextProcessorInterface $processor */
            $processor = new $processorClassName();
            $text = $processor->process($text);
        //} else {
        //    error_log('Skipped ' . $processorClassName . ' because it does not implement ' . self::TEXT_PROCESSOR_INTERFACE_FQN);
        //}
        return $text;
    }

    protected function prepareInstance()
    {
        $this->processorId = (int) $this->processorId;
    }
}
