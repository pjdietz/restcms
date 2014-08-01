<?php

namespace pjdietz\RestCms\Models;

use PDO;
use pjdietz\RestCms\Database\Database;
use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\TextProcessors\TextProcessorInterface;

class ProcessorModel extends RestCmsBaseModel
{
    const TEXT_PROCESSOR_INTERFACE_FQN = '\\pjdietz\\RestCms\\TextProcessors\\TextProcessorInterface';

    public $processorId;
    public $processorName;
    public $description;
    public $className;

    /**
     * @param string $processorName
     * @throws ResourceException
     * @return ProcessorModel
     */
    public static function initWithName($processorName)
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
WHERE
    p.processorName = :processorName
LIMIT 1;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':processorName', $processorName, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new ResourceException(
                "No Processor with name $processorName",
                ResourceException::NOT_FOUND);
        }
        return $stmt->fetchObject(get_called_class());
    }

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
WHERE
    a.articleId = :articleId
ORDER BY
    ap.sortOrder,
    p.processorName;
SQL;
            $stmt = $db->prepare($query);
        }
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public function process($text)
    {
        $processorClassName = $this->className;
        $processor = new $processorClassName();
        if ($processor instanceof TextProcessorInterface) {
            /** @var TextProcessorInterface $processor */
            $text = $processor->process($text);
        } else {
            error_log('Skipped ' . $processorClassName . ' because it does not implement ' . self::TEXT_PROCESSOR_INTERFACE_FQN);
        }
        return $text;
    }

    protected function prepareInstance()
    {
        $this->processorId = (int) $this->processorId;
    }
}
