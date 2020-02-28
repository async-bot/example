<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\PackagistFinder\ValueObject\SearchResult as Result;

final class SearchResult
{
    public function format(Result $searchResult): Message
    {
        return (new Message())
            ->appendNode(new Text('• [ '))
            ->appendNode((new Url($searchResult->getRepositoryUrl()))
                ->appendNode((new Bold())
                    ->appendNode(
                        new Text(sprintf('%s/%s', $searchResult->getPackageName()->getVendor(), $searchResult->getPackageName()->getPackage())),
                    )
                )
            )->appendNode(new Text(' ]'))
            ->appendNode(new Text(' | '))
            ->appendNode(new Text($searchResult->getDescription()))
            ->appendNode(new Text(sprintf(' (★ %d)', $searchResult->getNumberOfFavorites())))
        ;
    }
}
