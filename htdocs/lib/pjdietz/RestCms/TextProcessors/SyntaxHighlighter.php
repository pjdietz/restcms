<?php

namespace pjdietz\RestCms\TextProcessors;

/**
 * Convert GFM-style code blocks to HTML for use with SyntaxHighlighter
 */
class SyntaxHighlighter implements TextProcessorInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function transform($text)
    {
        $pattern = <<<'RE'
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
        $callback = function ($matches) {
            return sprintf(
                '<pre class="brush: %s">%s</pre>',
                trim($matches[2]),
                htmlspecialchars(trim($matches[3]))
            );
        };
        return preg_replace_callback($pattern, $callback, $text);
    }
}
