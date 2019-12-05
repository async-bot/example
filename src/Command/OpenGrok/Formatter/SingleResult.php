<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Formatter;

use AsyncBot\Plugin\OpenGrok\ValueObject\SearchResult;

abstract class SingleResult
{
    public function format(SearchResult $searchResult): string
    {
        return sprintf(
            '[%s::%d](%s) `%s`',
            $searchResult->getFilename(),
            $searchResult->getLineNumber(),
            sprintf('https://heap.space/xref/%s#%d', $searchResult->getFilename(), $searchResult->getLineNumber()),
            trim($searchResult->getLine()),
        );
    }
}
