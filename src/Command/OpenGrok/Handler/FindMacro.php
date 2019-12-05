<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Handler;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\OpenGrok\Responder\Macros;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;
use AsyncBot\Plugin\OpenGrok\Plugin;
use function Amp\call;

final class FindMacro
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
    public function handle(string $macro): Promise
    {
        return call(function () use ($macro) {
            /** @var SearchResults $searchResults */
            $searchResults = yield $this->plugin->findMacro($macro);

            yield (new Macros($this->bot))->respond($searchResults);
        });
    }
}
