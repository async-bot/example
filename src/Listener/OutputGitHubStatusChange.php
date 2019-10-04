<?php declare(strict_types=1);

namespace AsyncBot\Example\Listener;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Plugin\GitHubStatus\Event\Data\ComponentIssue;
use AsyncBot\Plugin\GitHubStatus\Event\Data\Status;
use AsyncBot\Plugin\GitHubStatus\Event\Listener\StatusChange;

final class OutputGitHubStatusChange implements StatusChange
{
    private Driver $bot;

    public function __construct(Driver $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return Promise<null>
     */
    public function __invoke(Status $status): Promise
    {
        if (!$status->hasActiveIncident()) {
            return $this->bot->postMessage('[tag:github-status] All issues have been resolved!');
        }

        return $this->bot->postMessage(
            sprintf(
                '[tag:github-status] %s | %s',
                $status->getOverallStatus(),
                implode(' | ', $this->formatActiveIssues($status)),
            ),
            );
    }

    /**
     * @return array<string>
     */
    public function formatActiveIssues(Status $status): array
    {
        $formattedIssues = [];

        /** @var ComponentIssue $issue */
        foreach ($status->getIssues() as $issue) {
            $formattedIssues[] = sprintf('%s has %s', $issue->getName(), $issue->getIssue());
        }

        return $formattedIssues;
    }
}
