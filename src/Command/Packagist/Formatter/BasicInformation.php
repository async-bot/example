<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Core\Message\Node\Bold;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Tag;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\PackagistFinder\ValueObject\Package;

final class BasicInformation
{
    public function format(Package $package): Message
    {
        return (new Message())
            ->appendNode(new Text('[ '))
            ->appendNode($this->getUrl($package))
            ->appendNode(new Text(' ] '))
            ->appendNode((new Tag())->appendNode(new Text($this->formatDataForTag($package->getLanguage()))))
            ->appendNode(new Text(' '))
            ->appendNode((new Tag())->appendNode(new Text($this->formatDataForTag($package->getType()))))
            ->appendNode(new Separator())
            ->appendNode(new Text($package->getDescription()))
            ->appendNode(new Separator())
            ->appendNode(new Text(sprintf('★ %d', $package->getGitHubInformation()->getNumberOfStars())))
        ;
    }

    private function getUrl(Package $package): Url
    {
        $text = sprintf('%s/%s', $package->getPackageName()->getVendor(), $package->getPackageName()->getPackage());

        return (new Url($package->getRepositoryUrl()))->appendNode((new Bold())->appendNode(new Text($text)));
    }

    private function formatDataForTag(string $data): string
    {
        return str_replace(' ', '-', strtolower($data));
    }
}
