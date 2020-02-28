<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Example\Command\OpenGrok\Formatter\Result;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;
use function Amp\call;

abstract class Responder
{
    protected Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(SearchResults $searchResults): Promise
    {
        return call(function () use ($searchResults) {
            if (count($searchResults) === 1) {
                return $this->bot->postMessage((new Result())->format(
                    $searchResults->getFirst(),
                ));
            }

            $headingMessage = (new Message())
                ->appendNode(new Text(sprintf('Total number of search results: %d', $searchResults->count())))
            ;

            if (count($searchResults) > 5) {
                $headingMessage
                    ->appendNode(new Text('. '))
                    ->appendNode(new Text('Showing the first 5 results.'))
                ;
            }

            yield $this->bot->postMessage($headingMessage);

            foreach ($searchResults as $index => $searchResult) {
                if ($index === 5) {
                    return null;
                }

                yield $this->bot->postMessage(
                    (new Result())->format($searchResult)->prependNode(new Text('• ')),
                );
            }

            return null;
        });
    }
}
