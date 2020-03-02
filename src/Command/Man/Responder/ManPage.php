<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Man\Responder;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\BlockQuote;
use AsyncBot\Core\Message\Node\Code;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Text;
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
            return $this->bot->postMessage(
                (new Message())->appendNode(new Text('Could not find the manual')),
            );
        }

        return call(function () use ($manualPage) {
            yield $this->bot->postMessage(
                (new Message())
                    ->appendNode((new Code())->appendNode(new Text($manualPage->getName())))
                    ->appendNode(new Separator())
                    ->appendNode(new Text($manualPage->getShortDescription())),
            );

            yield $this->bot->postMessage(
                (new Message())->appendNode((new Code())->appendNode(new Text($manualPage->getSynopsis()))),
            );

            yield $this->bot->postMessage(
                (new Message())->appendNode((new BlockQuote())->appendNode(new Text($manualPage->getLongDescription()))),
            );
        });
    }
}
