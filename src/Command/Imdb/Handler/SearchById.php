<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Handler;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\Imdb\Responder\Title as Responder;
use AsyncBot\Plugin\Imdb\Plugin;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Title;
use function Amp\call;

final class SearchById
{
    private Driver $bot;

    private Plugin $plugin;

    public function __construct(Driver $bot, Plugin $plugin)
    {
        $this->bot    = $bot;
        $this->plugin = $plugin;
    }

    /**
     * @return Promise<null>
     */
    public function handle(string $imdbId): Promise
    {
        return call(function () use ($imdbId) {
            /** @var Title|null $title */
            $title = yield $this->getTitle($imdbId);

            yield (new Responder($this->bot))->respond($title);
        });
    }

    /**
     * @return Promise<?Title>
     */
    private function getTitle(string $imdbId): Promise
    {
        return call(function () use ($imdbId) {
            try {
                return yield $this->plugin->getByImdbId($imdbId);
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
