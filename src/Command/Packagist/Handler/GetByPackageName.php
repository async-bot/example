<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Packagist\Handler;

use Amp\Promise;
use AsyncBot\Core\Driver;
use AsyncBot\Example\Command\Packagist\Responder\Package as Responder;
use AsyncBot\Plugin\PackagistFinder\Plugin;
use AsyncBot\Plugin\PackagistFinder\ValueObject\Package;
use function Amp\call;

final class GetByPackageName
{
    private Driver $bot;

    private Plugin $plugin;

    public function __construct(Driver $bot, Plugin $plugin)
    {
        $this->bot    = $bot;
        $this->plugin = $plugin;
    }

    /**
     * @return Promise<null>
     */
    public function handle(string $packageName): Promise
    {
        return call(function () use ($packageName) {
            /** @var Package $package */
            $package = yield $this->getPackage($packageName);

            yield (new Responder($this->bot))->respond($package);
        });
    }

    /**
     * @return Promise<Package>
     */
    private function getPackage(string $packageName): Promise
    {
        return call(function () use ($packageName) {
            try {
                return yield $this->plugin->getByPackageName($packageName);
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
