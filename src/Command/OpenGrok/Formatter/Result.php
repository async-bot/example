<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Code;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\OpenGrok\ValueObject\SearchResult;

final class Result
{
    public function format(SearchResult $searchResult): Message
    {
        return (new Message())
            ->appendNode(new Text('[ '))
            ->appendNode($this->getUrl($searchResult))
            ->appendNode(new Text(' ] '))
            ->appendNode((new Code())->appendNode(new Text(html_entity_decode($searchResult->getLine()))))
        ;
    }

    private function getUrl(SearchResult $searchResult): Url
    {
        $href = sprintf(
            'https://heap.space/xref/%s#%d',
            ltrim($searchResult->getFilename(), '/'),
            $searchResult->getLineNumber(),
        );

        $text = sprintf('%s::%d', $searchResult->getFilename(), $searchResult->getLineNumber());

        return (new Url($href))->appendNode((new Bold())->appendNode(new Text($text)));
    }
}
