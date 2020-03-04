<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Google\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Event\Data\Message;
use AsyncBot\Driver\StackOverflowChat\Event\Listener\MessagePosted;
use AsyncBot\Example\Command\Google\Handler\Search;
use AsyncBot\Plugin\GoogleSearch\Plugin;

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
        if (strpos($message->getContent(), '!!google') !== 0 && strpos($message->getContent(), '!!?') !== 0) {
            return new Success();
        }

        preg_match('~^(?:!!google |!!\? )(.+)~', $message->getContent(), $matches);

        return (new Search($this->bot, $this->plugin))->handle($matches[1]);
    }
}
