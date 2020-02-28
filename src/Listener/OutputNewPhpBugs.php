<?php declare(strict_types=1);

namespace AsyncBot\Example\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use AsyncBot\Plugin\PhpBugs\Event\Listener\NewBugs;
use function Amp\call;

final class OutputNewPhpBugs implements NewBugs
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Bugs $bugs): Promise
    {
        if (!count($bugs)) {
            return new Success();
        }

        return call(function () use ($bugs) {
            foreach ($bugs as $bug) {
                $message = (new Message())->appendNode(new Text('[tag:php] '));

                if ($bug->getType()->equals(new Type(Type::DOCUMENTATION_PROBLEM))) {
                    $message->appendNode(new Text('[tag:doc-bug] '));
                } else {
                    $message->appendNode(new Text('[tag:bug] '));
                }

                $message
                    ->appendNode(new Text($bug->getSummary()))
                    ->appendNode(new Text(' ・ '))
                    ->appendNode(new Text($bug->getPackage()))
                    ->appendNode(new Text(' ・ '))
                    ->appendNode((new Url($bug->getUrl()))->appendNode(new Text('#' . $bug->getId())))
                ;

                yield $this->bot->postMessage($message);
            }
        });
    }
}
