<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Xkcd\Handler;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Example\Command\Xkcd\Responder\Render;
use AsyncBot\Plugin\Xkcd\Plugin;
use function Amp\call;

final class Find
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
    public function handle(string $keywords): Promise
    {
        return call(function () use ($keywords) {
            $comic = yield $this->plugin->findByKeywords($keywords);

            yield (new Render($this->bot))->respond($comic);
        });
    }
}
