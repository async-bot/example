<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\OpenGrok\Handler;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\OpenGrok\Responder\Functions;
use AsyncBot\Plugin\OpenGrok\Collection\SearchResults;
use AsyncBot\Plugin\OpenGrok\Plugin;
use function Amp\call;

final class FindFunction
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
    public function handle(string $function): Promise
    {
        return call(function () use ($function) {
            /** @var SearchResults $searchResults */
            $searchResults = yield $this->plugin->findFunction($function);

            yield (new Functions($this->bot))->respond($searchResults);
        });
    }
}
