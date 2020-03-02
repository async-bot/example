<?php declare(strict_types=1);

namespace AsyncBot\Example\Listener;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Tag;
use AsyncBot\Core\Message\Node\Text;
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
            return $this->bot->postMessage(
                (new Message())
                    ->appendNode((new Tag())->appendNode(new Text('github-status')))
                    ->appendNode(new Text(' All issues have been resolved!')),
            );
        }

        $activeIssuesMessage = (new Message())
            ->appendNode((new Tag())->appendNode(new Text('github-status')))
            ->appendNode(new Text(' '))
            ->appendNode(new Text($status->getOverallStatus()))
        ;

        foreach ($this->formatActiveIssues($status) as $activeIssue) {
            $activeIssuesMessage
                ->appendNode(new Separator())
                ->appendNode($activeIssue)
            ;
        }

        return $this->bot->postMessage($activeIssuesMessage);
    }

    /**
     * @return array<Text>
     */
    public function formatActiveIssues(Status $status): array
    {
        $formattedIssues = [];

        /** @var ComponentIssue $issue */
        foreach ($status->getIssues() as $issue) {
            $formattedIssues[] = new Text(
                sprintf('%s has %s', $issue->getName(), $issue->getIssue()),
            );
        }

        return $formattedIssues;
    }
}
