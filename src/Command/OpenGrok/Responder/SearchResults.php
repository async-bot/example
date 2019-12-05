<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleFunction;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleMacro;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleMethod;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleResult;
use AsyncBot\Example\Command\OpenGrok\Formatter\SingleUnclassified;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults as Collection;
use AsyncBot\Plugin\OpenGrok\ValueObject\FunctionDefinition;
use AsyncBot\Plugin\OpenGrok\ValueObject\MacroDefinition;
use AsyncBot\Plugin\OpenGrok\ValueObject\MethodDefinition;
use AsyncBot\Plugin\OpenGrok\ValueObject\SearchResult;
use function Amp\call;

final class SearchResults
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(Collection $searchResults): Promise
    {
        return call(function () use ($searchResults) {
            if (count($searchResults) === 0) {
                return $this->bot->postMessage('Could not find any results');
            }

            if (count($searchResults) === 1) {
                return $this->bot->postMessage($this->getFormatter($searchResults->getFirst())->format(
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
                    sprintf('• %s', $this->getFormatter($searchResult)->format($searchResult)),
                );
            }

            return null;
        });
    }

    private function getFormatter(SearchResult $searchResult): SingleResult
    {
        if ($searchResult instanceof MethodDefinition) {
            return new SingleMethod();
        }

        if ($searchResult instanceof MacroDefinition) {
            return new SingleMacro();
        }

        if ($searchResult instanceof FunctionDefinition) {
            return new SingleFunction();
        }

        return new SingleUnclassified();
    }
}
