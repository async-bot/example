<?php declare(strict_types=1);

namespace AsyncBot\Example\Bin;

use Amp\Http\Client\Client;
use AsyncBot\Core\Logger\Factory as LoggerFactory;
use AsyncBot\Core\Manager;
use AsyncBot\Driver\StackOverflowChat\Authentication\ValueObject\Credentials;
use AsyncBot\Driver\StackOverflowChat\Driver;
use AsyncBot\Driver\StackOverflowChat\Factory as StackOverflowChatDriverFactory;
use AsyncBot\Example\Command\Imdb\Listener\Listener as ImdbCommandListener;
use AsyncBot\Example\Listener\OutputGitHubStatusChange;
use AsyncBot\Example\Listener\OutputTimerInformation;
use AsyncBot\Plugin\GitHubStatus\Parser\Html;
use AsyncBot\Plugin\GitHubStatus\Plugin as GitHubStatusPlugin;
use AsyncBot\Plugin\GitHubStatus\Retriever\Http;
use AsyncBot\Plugin\GitHubStatus\Storage\InMemoryRepository;
use AsyncBot\Plugin\Imdb\Plugin as ImdbPlugin;
use AsyncBot\Plugin\Imdb\ValueObject\ApiKey;
use AsyncBot\Plugin\Timer\Plugin as TimerPlugin;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Set up logger
 */
$logger = LoggerFactory::buildConsoleLogger();

/**
 * Set up the HTTP client
 */
$httpClient = new Client();
$fwHttpClient = new \AsyncBot\Core\Http\Client($httpClient);

/**
 * Get the configuration
 */
$configuration = require_once __DIR__ . '/../config.php';

/**
 * Set up bot(s)
 */
$stackOverflowChatBot = (new StackOverflowChatDriverFactory(
    $httpClient,
    new Credentials(
        $configuration['drivers'][Driver::class]['username'],
        $configuration['drivers'][Driver::class]['password'],
        $configuration['drivers'][Driver::class]['roomUrl'],
    ),
))->build();

/**
 * Set up plugin(s)
 */
$imdbPlugin = new ImdbPlugin($fwHttpClient, new ApiKey($configuration['apis']['omdbApiKey']));

/**
 * Set up runnable plugin(s)
 */
$timerPlugin        = new TimerPlugin($logger, new \DateInterval('PT15M'));
$gitHubStatusPlugin = new GitHubStatusPlugin($logger, new Http($httpClient, new Html()), new InMemoryRepository());

/**
 * Register for events
 */
$timerPlugin->onTick(new OutputTimerInformation($stackOverflowChatBot));
$gitHubStatusPlugin->onStatusChange(new OutputGitHubStatusChange($stackOverflowChatBot));

/**
 * Add listeners for commands
 */
$stackOverflowChatBot->onNewMessage(new ImdbCommandListener($stackOverflowChatBot, $imdbPlugin));

/**
 * Run the bot minions
 */
(new Manager($logger))
    ->registerBot($stackOverflowChatBot)
    ->registerPlugin($timerPlugin)
    ->registerPlugin($gitHubStatusPlugin)
    ->run()
;
