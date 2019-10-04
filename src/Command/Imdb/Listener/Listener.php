<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Event\Data\Message;
use AsyncBot\Driver\StackOverflowChat\Event\Listener\MessagePosted;
use AsyncBot\Example\Command\Imdb\Handler\SearchById;
use AsyncBot\Example\Command\Imdb\Handler\SearchByTitle;
use AsyncBot\Plugin\Imdb\Plugin;

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
        if (strpos($message->getContent(), '!!imdb ') !== 0) {
            return new Success();
        }

        if (preg_match('~^!!imdb (?:tt)?-?(\d{7,8})$~', $message->getContent(), $matches) === 1) {
            return (new SearchById($this->bot, $this->plugin))->handle($matches[1]);
        }

        preg_match('~^!!imdb (.+)~', $message->getContent(), $matches);

        return (new SearchByTitle($this->bot, $this->plugin))->handle($matches[1]);
    }
}
