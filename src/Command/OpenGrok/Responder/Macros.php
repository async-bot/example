<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Responder;

use Amp\Promise;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;

final class Macros extends Responder
{
    public function respond(SearchResults $searchResults): Promise
    {
        if (count($searchResults) === 0) {
            return $this->bot->postMessage(
                (new Message())->appendNode(new Text('Could not find the macro definition')),
            );
        }

        return parent::respond($searchResults);
    }
}
