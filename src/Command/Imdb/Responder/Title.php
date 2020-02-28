<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Responder;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\BlockQuote;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Example\Command\Imdb\Formatter\TitleInformation as Formatter;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Title as TitleInformation;
use function Amp\call;

final class Title
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(?TitleInformation $title): Promise
    {
        if ($title === null) {
            return $this->bot->postMessage(
                (new Message())->appendNode(new Text('Could not find the movie or series')),
            );
        }

        return call(function () use ($title) {
            yield $this->bot->postMessage(
                (new Message())->appendNode(new Text($title->getPoster())),
            );

            yield $this->bot->postMessage((new Formatter())->format($title));

            yield $this->bot->postMessage(
                (new Message())->appendNode(
                    (new BlockQuote())->appendNode(new Text($title->getPlot())),
                ),
            );
        });
    }
}
