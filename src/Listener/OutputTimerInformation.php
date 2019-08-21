<?php declare(strict_types=1);

namespace AsyncBot\Example\Listener;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Plugin\Timer\Event\Data\Tick as EventData;
use AsyncBot\Plugin\Timer\Event\Listener\Tick;

final class OutputTimerInformation implements Tick
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return Promise<null>
     */
    public function __invoke(EventData $eventData): Promise
    {
        if ($eventData->getPreviousTimestamp() === null) {
            return $this->bot->postMessage(
                sprintf('The current tick ran at %s and it was the first tick', $eventData->getCurrentTimestamp()->format('H:i:s.u')),
            );
        }

        return $this->bot->postMessage(
            sprintf(
                'The current tick ran at %s, the previous tick ran at %s',
                $eventData->getCurrentTimestamp()->format('H:i:s.u'),
                $eventData->getPreviousTimestamp()->format('H:i:s.u'),
            ),
        );
    }
}
