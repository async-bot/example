<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\PackagistFinder\ValueObject\Package;

final class BasicInformation
{
    public function format(Package $package): Message
    {
        return (new Message())
            ->appendNode(new Text('[ '))
            ->appendNode((new Url($package->getRepositoryUrl()))
                ->appendNode((new Bold())
                    ->appendNode(
                        new Text(sprintf('%s/%s', $package->getPackageName()->getVendor(), $package->getPackageName()->getPackage())),
                    )
                )
            )->appendNode(new Text(' ] '))
            ->appendNode(new Text(sprintf('[tag:%s]', $this->formatDataForTag($package->getLanguage()))))
            ->appendNode(new Text(' '))
            ->appendNode(new Text(sprintf('[tag:%s]', $this->formatDataForTag($package->getType()))))
            ->appendNode(new Text(' | '))
            ->appendNode(new Text($package->getDescription()))
            ->appendNode(new Text(sprintf(' (★ %d)', $package->getGitHubInformation()->getNumberOfStars())))
        ;
    }

    private function formatDataForTag(string $data): string
    {
        return str_replace(' ', '-', strtolower($data));
    }
}
