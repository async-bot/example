<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\PackagistFinder\ValueObject\SearchResult as Result;

final class SearchResult
{
    public function format(Result $searchResult): Message
    {
        return (new Message())
            ->appendNode(new Text('• [ '))
            ->appendNode($this->getUrl($searchResult))
            ->appendNode(new Text(' ]'))
            ->appendNode(new Separator())
            ->appendNode(new Text($searchResult->getDescription()))
            ->appendNode(new Separator())
            ->appendNode(new Text(sprintf('★ %d', $searchResult->getNumberOfFavorites())))
        ;
    }

    private function getUrl(Result $searchResult): Url
    {
        $text = sprintf(
            '%s/%s',
            $searchResult->getPackageName()->getVendor(),
            $searchResult->getPackageName()->getPackage(),
        );

        return (new Url($searchResult->getRepositoryUrl()))->appendNode((new Bold())->appendNode(new Text($text)));
    }
}
