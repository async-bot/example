<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults as Collection;

final class SearchResults extends Responder
{
    public function respond(Collection $searchResults): Promise
    {
        if (count($searchResults) === 0) {
            return $this->bot->postMessage(
                (new Message())->appendNode(new Text('Could not find any results')),
            );
        }

        return parent::respond($searchResults);
    }
}
