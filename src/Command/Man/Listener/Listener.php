<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Man\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Event\Data\Message;
use AsyncBot\Driver\StackOverflowChat\Event\Listener\MessagePosted;
use AsyncBot\Example\Command\Man\Handler\Search;
use AsyncBot\Plugin\LinuxManualPages\Plugin;

final class Listener implements MessagePosted
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
    public function __invoke(Message $message): Promise
    {
        if (preg_match('~^!!man (.+)$~', $message->getContent(), $matches) !== 1) {
            return new Success();
        }

        return (new Search($this->bot, $this->plugin))->handle($matches[1]);
    }
}
