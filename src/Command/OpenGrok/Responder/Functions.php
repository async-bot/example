<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleFunction;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;
use function Amp\call;

final class Functions
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(SearchResults $searchResults): Promise
    {
        return call(function () use ($searchResults) {
            if (count($searchResults) === 0) {
                return $this->bot->postMessage('Could not find the function definition');
            }

            if (count($searchResults) === 1) {
                return $this->bot->postMessage((new SingleFunction())->format(
                    $searchResults->getFirst(),
                ));
            }

            yield $this->bot->postMessage(
                sprintf('Total number of search results: %d. Showing the first 5 results.', count($searchResults)),
            );

            foreach ($searchResults as $index => $searchResult) {
                if ($index === 5) {
                    return null;
                }

                yield $this->bot->postMessage(
                    sprintf('• %s', (new SingleFunction())->format($searchResult)),
                );
            }

            return null;
        });
    }
}
