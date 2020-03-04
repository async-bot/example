<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Wikipedia\Responder;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\Wikipedia\Collection\Search\OpenSearchResults;
use function Amp\call;

final class SearchResults
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return Promise<null>
     */
    public function respond(OpenSearchResults $searchResults): Promise
    {
        if (!$searchResults->count()) {
            return $this->bot->postMessage(
                (new Message())->appendNode(new Text('Could not find the topic')),
            );
        }

        return call(function () use ($searchResults) {
            if ($searchResults->count() === 1) {
                return $this->bot->postMessage(
                    (new Message())->appendNode(
                        (new Url($searchResults->getFirstResult()->getUrl()))->appendNode(
                            (new Bold())->appendNode(new Text($searchResults->getFirstResult()->getTitle()))
                        ),
                    ),
                );
            }

            $headingMessage = (new Message())
                ->appendNode(new Text(sprintf('Total number of search results: %d', $searchResults->count())))
            ;

            if ($searchResults->count() > 5) {
                $headingMessage
                    ->appendNode(new Text('. '))
                    ->appendNode(new Text('Showing the first 5 results.'))
                ;
            }

            yield $this->bot->postMessage($headingMessage);

            $formattedResults = new Message();

            foreach ($searchResults as $index => $searchResult) {
                if ($index === 5) {
                    break;
                }

                if ($index !== 0) {
                    $formattedResults->appendNode(new Separator());
                }

                $formattedResults->appendNode((new Url($searchResult->getUrl()))->appendNode(
                    (new Bold())->appendNode(new Text($searchResult->getTitle())),
                ));
            }

            return $this->bot->postMessage($formattedResults);
        });
    }
}
