<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Xkcd\Handler;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Example\Command\Xkcd\Responder\Render;
use AsyncBot\Plugin\Xkcd\Plugin;
use function Amp\call;

final class GetById
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
    public function handle(int $id): Promise
    {
        return call(function () use ($id) {
            $comic = yield $this->plugin->getById($id);

            yield (new Render($this->bot))->respond($comic);
        });
    }
}
