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
        return $this->bot->postMessage(
            sprintf('The current time is %s', $eventData->getCurrentTimestamp()->format('H:i:s.u')),
            );
    }
}
