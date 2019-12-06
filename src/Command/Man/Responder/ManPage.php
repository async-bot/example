<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Man\Responder;

use Amp\Promise;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Plugin\LinuxManualPages\ValueObject\ManualPage;
use function Amp\call;

final class ManPage
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    public function respond(?ManualPage $manualPage): Promise
    {
        if ($manualPage === null) {
            return $this->bot->postMessage('Could not find the manual');
        }

        return call(function () use ($manualPage) {
            yield $this->bot->postMessage(sprintf(
                '`%s` - %s',
                $manualPage->getName(),
                $manualPage->getShortDescription(),
            ));

            yield $this->bot->postMessage(
                sprintf('`%s`', $manualPage->getSynopsis()),
            );

            yield $this->bot->postMessage(
                sprintf('> %s', $manualPage->getLongDescription()),
            );
        });
    }
}
