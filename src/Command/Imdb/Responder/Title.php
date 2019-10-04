<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
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
            return $this->bot->postMessage('Could not find the movie or series');
        }

        return call(function () use ($title) {
            yield $this->bot->postMessage($title->getPoster());
            yield $this->bot->postMessage((new Formatter())->format($title));
            yield $this->bot->postMessage('> ' . $title->getPlot());
        });
    }
}
