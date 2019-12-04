<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\WordOfTheDay\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Event\Data\Message;
use AsyncBot\Driver\StackOverflowChat\Event\Listener\MessagePosted;
use AsyncBot\Plugin\WordOfTheDay\Plugin;
use AsyncBot\Plugin\WordOfTheDay\ValueObject\Result\WordOfTheDay;
use function Amp\call;

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
        if ($message->getContent() !== '!!wotd') {
            return new Success();
        }

        return call(function () {
            /** @var WordOfTheDay $wordOfTheDay */
            $wordOfTheDay = yield $this->plugin->getFromMerriamWebster();

            yield $this->bot->postMessage(
                sprintf(
                    '[**%s**](%s) %s',
                    $wordOfTheDay->getWord(),
                    $wordOfTheDay->getUrl(),
                    $wordOfTheDay->getDefinition(),
                ),
            );
        });
    }
}
