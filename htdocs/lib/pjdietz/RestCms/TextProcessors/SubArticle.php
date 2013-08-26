<?php

namespace pjdietz\RestCms\TextProcessors;

use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Util\Template;

class SubArticle implements TextProcessorInterface
{
    const ARTICLE_PATTERN = '/\{\{\/articles\/([0-9a-z_-]+)\}\}/';

    /**
     * @param string $text
     * @return string
     */
    public function transform($text)
    {
        // Build an array of merge fields.
        $mergeFields = array();

        // Search the text for references to articles.
        preg_match(self::ARTICLE_PATTERN, $text, $matches);

        if ($matches) {
            $articleId = $matches[1];
            try {
                $article = ArticleModel::init($articleId);
                $mergeFields[$matches[0]] = $article->content;
            } catch (ResourceException $e) {
                // Skip. Don't throw an exception, but don't replace either.
            }
        }

        return Template::stringFromTemplate($text, $mergeFields);
    }
}
