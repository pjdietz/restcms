<?php

namespace pjdietz\RestCms\TextProcessors;

/**
 * Convert GFM-style code blocks to HTML for use with SyntaxHighlighter
 */
class SyntaxHighlighter implements TextProcessorInterface
{
    const DEFAULT_MARKUP = '<pre class="brush: %s">%s</pre>';
    const DEFAULT_REGEX = <<<'RE'
{
    # Start of line or sample.
    ^

    # \1: Opening marker. Three or more tildes or tick marks
    (~{3,}|`{3,})

    # \2: Rest of the line.
    (.*)$\n

    # \3: Content. Non-greedy everything grabs all up to...
    ?([\s\S]*)

    # Ending marker.
    \1

}mx
RE;

    public $markup = self::DEFAULT_MARKUP;
    public $regex = self::DEFAULT_REGEX;

    /**
     * @param string $text
     * @return string
     */
    public function transform($text)
    {
        $markup = $this->markup;
        $callback = function ($matches) use ($markup) {
            return sprintf(
                $markup,
                trim($matches[2]),
                htmlspecialchars(trim($matches[3]))
            );
        };
        return preg_replace_callback($this->regex, $callback, $text);
    }
}
