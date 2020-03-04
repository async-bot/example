<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Google\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\GoogleSearch\ValueObject\Result\SearchResult;

final class Result
{
    public function format(SearchResult $result): Message
    {
        return (new Message())
            ->appendNode(new Text('[ '))
            ->appendNode((new Url($result->getUrl()))->appendNode(
                (new Bold())->appendNode(new Text($result->getTitle())),
            ))
            ->appendNode(new Text('] '))
            ->appendNode(new Text($result->getDescription()))
        ;
    }
}
