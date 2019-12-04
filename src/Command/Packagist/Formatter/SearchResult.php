<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Plugin\PackagistFinder\ValueObject\SearchResult as Result;

final class SearchResult
{
    public function format(Result $searchResult): string
    {
        return sprintf(
            '• [ [**%s/%s**](%s) ] | %s (★ %d)',
            $searchResult->getPackageName()->getVendor(),
            $searchResult->getPackageName()->getPackage(),
            $searchResult->getRepositoryUrl(),
            $searchResult->getDescription(),
            $searchResult->getNumberOfFavorites(),
        );
    }
}
