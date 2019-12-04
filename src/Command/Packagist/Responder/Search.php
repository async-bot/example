<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\Packagist\Formatter\SearchResult;
use AsyncBot\Plugin\PackagistFinder\Collection\SearchResults;
use function Amp\call;

final class Search
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(SearchResults $searchResults): Promise
    {
        return call(function () use ($searchResults) {
            yield $this->bot->postMessage(
                sprintf('Total number of search results: %d. Showing the first 5 results.', $searchResults->getTotalNumberOfResults()),
            );

            foreach ($searchResults as $index => $searchResult) {
                if ($index === 5) {
                    break;
                }

                yield $this->bot->postMessage((new SearchResult())->format($searchResult));
            }
        });
    }
}
