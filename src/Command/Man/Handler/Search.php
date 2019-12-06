<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Man\Handler;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Example\Command\Man\Responder\ManPage;
use AsyncBot\Plugin\LinuxManualPages\Plugin;
use AsyncBot\Plugin\LinuxManualPages\ValueObject\ManualPage;
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
    public function handle(string $command): Promise
    {
        return call(function () use ($command) {
            /** @var ManualPage|null $title */
            $manualPage = yield $this->plugin->search($command);

            yield (new ManPage($this->bot))->respond($manualPage);
        });
    }
}
