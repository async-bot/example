<?php declare(strict_types=1);

namespace AsyncBot\Example\Bin;

use Amp\Http\Client\HttpClientBuilder;
use AsyncBot\Core\Http\Client;
use AsyncBot\Core\Logger\Factory as LoggerFactory;
use AsyncBot\Core\Manager;
use AsyncBot\Driver\StackOverflowChat\Authentication\ValueObject\Credentials;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Factory as StackOverflowChatDriverFactory;
use AsyncBot\Example\Command\Imdb\Listener\Listener as ImdbCommandListener;
use AsyncBot\Example\Command\Man\Listener\Listener as ManListener;
use AsyncBot\Example\Command\OpenGrok\Listener\Listener as OpenGrokListener;
use AsyncBot\Example\Command\Packagist\Listener\Listener as PackagistFinderListener;
use AsyncBot\Example\Command\WordOfTheDay\Listener\Listener as WordOfTheDayCommandListener;
use AsyncBot\Example\Listener\OutputGitHubStatusChange;
use AsyncBot\Example\Listener\OutputTimerInformation;
use AsyncBot\Plugin\GitHubStatus\Parser\Html;
use AsyncBot\Plugin\GitHubStatus\Plugin as GitHubStatusPlugin;
use AsyncBot\Plugin\GitHubStatus\Retriever\Http;
use AsyncBot\Plugin\GitHubStatus\Storage\InMemoryRepository;
use AsyncBot\Plugin\Imdb\Plugin as ImdbPlugin;
use AsyncBot\Plugin\Imdb\ValueObject\ApiKey;
use AsyncBot\Plugin\LinuxManualPages\Plugin as LinuxManualPagesPlugin;
use AsyncBot\Plugin\OpenGrok\Plugin as OpenGrokPlugin;
use AsyncBot\Plugin\PackagistFinder\Plugin as PackagistFinderPlugin;
use AsyncBot\Plugin\PhpBugs\Parser\Html as PhpBugsParser;
use AsyncBot\Plugin\PhpBugs\Plugin as PhpBugsPlugin;
use AsyncBot\Plugin\PhpBugs\Retriever\GetAllBugs;
use AsyncBot\Plugin\PhpBugs\Storage\InMemoryRepository as PhpBugsStorage;
use AsyncBot\Plugin\Timer\Plugin as TimerPlugin;
use AsyncBot\Plugin\WordOfTheDay\Plugin as WordOfTheDayPlugin;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Set up logger
 */
$logger = LoggerFactory::buildConsoleLogger();

/**
 * Set up the HTTP client
 */
$httpClient = new Client(HttpClientBuilder::buildDefault());

/**
 * Get the configuration
 */
$configuration = require_once __DIR__ . '/../config.php';

/**
 * Set up bot(s)
 */
$stackOverflowChatBot = (new StackOverflowChatDriverFactory(
    new Credentials(
        $configuration['drivers'][Driver::class]['username'],
        $configuration['drivers'][Driver::class]['password'],
        $configuration['drivers'][Driver::class]['roomUrl'],
    ),
))->build();

/**
 * Set up plugin(s)
 */
$imdbPlugin         = new ImdbPlugin($httpClient, new ApiKey($configuration['apis']['omdbApiKey']));
$wordOfTheDayPlugin = new WordOfTheDayPlugin($httpClient);
$packagistPlugin    = new PackagistFinderPlugin($httpClient);
$openGrokPlugin     = new OpenGrokPlugin($httpClient);
$linuxManPlugin     = new LinuxManualPagesPlugin($httpClient);

/**
 * Set up runnable plugin(s)
 */
$timerPlugin        = new TimerPlugin($logger, new \DateInterval('PT15M'));
$gitHubStatusPlugin = new GitHubStatusPlugin($logger, new Http($httpClient, new Html()), new InMemoryRepository());
$phpBugsPlugin      = new PhpBugsPlugin($logger, new GetAllBugs($httpClient, new PhpBugsParser()), new PhpBugsStorage(), new \DateInterval('PT1M'));

/**
 * Register for events
 */
$timerPlugin->onTick(new OutputTimerInformation($stackOverflowChatBot));
$gitHubStatusPlugin->onStatusChange(new OutputGitHubStatusChange($stackOverflowChatBot));

/**
 * Add listeners for commands
 */
$stackOverflowChatBot->onNewMessage(new ImdbCommandListener($stackOverflowChatBot, $imdbPlugin));
$stackOverflowChatBot->onNewMessage(new WordOfTheDayCommandListener($stackOverflowChatBot, $wordOfTheDayPlugin));
$stackOverflowChatBot->onNewMessage(new PackagistFinderListener($stackOverflowChatBot, $packagistPlugin));
$stackOverflowChatBot->onNewMessage(new OpenGrokListener($stackOverflowChatBot, $openGrokPlugin));
$stackOverflowChatBot->onNewMessage(new ManListener($stackOverflowChatBot, $linuxManPlugin));

/**
 * Run the bot minions
 */
(new Manager($logger))
    ->registerBot($stackOverflowChatBot)
    ->registerPlugin($timerPlugin)
    ->registerPlugin($gitHubStatusPlugin)
    ->registerPlugin($phpBugsPlugin)
    ->run()
;
