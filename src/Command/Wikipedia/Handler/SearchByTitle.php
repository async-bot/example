<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Wikipedia\Handler;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\Wikipedia\Responder\SearchResults as Responder;
use AsyncBot\Plugin\Wikipedia\Collection\Search\OpenSearchResults;
use AsyncBot\Plugin\Wikipedia\Plugin;
use function Amp\call;

final class SearchByTitle
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
            /** @var OpenSearchResults $searchResults */
            $searchResults = yield $this->getTitle($keywords);

            yield (new Responder($this->bot))->respond($searchResults);
        });
    }

    /**
     * @return Promise<?Title>
     */
    private function getTitle(string $keywords): Promise
    {
        return call(function () use ($keywords) {
            try {
                return yield $this->plugin->openSearch($keywords);
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
