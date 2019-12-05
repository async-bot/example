<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleMacro;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;
use function Amp\call;

final class Macros
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
                return $this->bot->postMessage('Could not find the macro definition');
            }

            if (count($searchResults) === 1) {
                return $this->bot->postMessage((new SingleMacro())->format(
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
                    sprintf('• %s', (new SingleMacro())->format($searchResult)),
                );
            }

            return null;
        });
    }
}
