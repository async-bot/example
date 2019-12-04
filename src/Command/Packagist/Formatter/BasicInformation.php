<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Formatter;

use AsyncBot\Plugin\PackagistFinder\ValueObject\Package;

final class BasicInformation
{
    public function format(Package $package): string
    {
        return sprintf(
            '[ [**%s/%s**](%s) ] [tag:%s] [tag:%s] | %s (★ %d)',
            $package->getPackageName()->getVendor(),
            $package->getPackageName()->getPackage(),
            $package->getRepositoryUrl(),
            $this->formatDataForTag($package->getLanguage()),
            $this->formatDataForTag($package->getType()),
            $package->getDescription(),
            $package->getGitHubInformation()->getNumberOfStars(),
        );
    }

    private function formatDataForTag(string $data): string
    {
        return str_replace(' ', '-', strtolower($data));
    }
}
