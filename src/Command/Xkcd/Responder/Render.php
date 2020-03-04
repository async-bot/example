<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Xkcd\Responder;

use Amp\Delayed;
use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Italic;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\Xkcd\ValueObject\Comic;
use function Amp\call;

final class Render
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return Promise<null>
     */
    public function respond(?Comic $comic): Promise
    {
        if (!$comic) {
            return $this->bot->postMessage(
                (new Message())->appendNode(
                    new Text('Could not find comic'),
                ),
            );
        }

        return call(function () use ($comic) {
            $url = sprintf('https://xkcd.com/%d/', $comic->getNumber());

            yield $this->bot->postMessage(
                (new Message())
                    ->appendNode((new Url($url))->appendNode(
                        (new Bold())->appendNode(new Text($comic->getTitle())),
                    ))
            );

            yield $this->bot->postMessage(
                (new Message())->appendNode(new Text($comic->getImageUrl())),
            );

            // wait for the punchline
            yield new Delayed(5000);

            yield $this->bot->postMessage(
                (new Message())->appendNode(
                    (new Italic())->appendNode(new Text($comic->getAltText())),
                ),
            );
        });
    }
}
