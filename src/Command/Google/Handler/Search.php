<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Google\Handler;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Example\Command\Google\Formatter\Result;
use AsyncBot\Plugin\GoogleSearch\Collection\SearchResults;
use AsyncBot\Plugin\GoogleSearch\Plugin;
use function Amp\call;

final class Search
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
            $formatter = new Result();

            /** @var SearchResults $searchResults */
            $searchResults = yield $this->plugin->search($keywords);

            if (!$searchResults->count()) {
                return $this->bot->postMessage(
                    (new Message())
                        ->appendNode(new Text('No results found')),
                );
            }

            if ($searchResults->count() === 1) {
                return $this->bot->postMessage(
                    $formatter->format(reset($searchResults)),
                );
            }

            foreach ($searchResults as $index => $searchResult) {
                if ($index === 5) {
                    break;
                }

                yield $this->bot->postMessage(
                    $formatter->format($searchResult),
                );
            }

            return null;
        });
    }
}
