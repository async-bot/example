<?php declare(strict_types=1);

namespace AsyncBot\Example\Listener;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Core\Driver;
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
                $format = '[tag:php] [tag:bug] %s ・ %s';

                if ($bug->getType()->equals(new Type(Type::DOCUMENTATION_PROBLEM))) {
                    $format = '[tag:php] [tag:doc-bug] %s ・ %s';
                }

                yield $this->bot->postMessage(
                    sprintf(
                        $format,
                        $bug->getSummary(),
                        sprintf('%s ・ [#%d](%s)', $bug->getPackage(), $bug->getId(), $bug->getUrl()),
                    ),
                );
            }
        });
    }
}
