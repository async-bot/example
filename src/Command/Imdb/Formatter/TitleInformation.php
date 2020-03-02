<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Formatter;

use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Node;
use AsyncBot\Core\Message\Node\Separator;
use AsyncBot\Core\Message\Node\Tag;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Ratings;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Title;

final class TitleInformation
{
    public function format(Title $title): Message
    {
        $message = (new Message())
            ->appendNode($this->formatTitle($title))
            ->appendNode(new Text(' '))
            ->appendNode(new Text(sprintf('(%d)', $title->getYear())))
            ->appendNode(new Separator())
        ;

        foreach ($this->getGenreTags($title) as $index => $genreTag) {
            if ($index !== 0) {
                $message->appendNode(new Text(' '));
            }

            $message->appendNode($genreTag);
        }

        foreach ($this->getRatings($title->getRatings()) as $ratingNode) {
            $message->appendNode($ratingNode);
        }

        return $message;
    }

    private function formatTitle(Title $title): Url
    {
        $url = new Url($title->getWebsite() ?? sprintf('https://www.imdb.com/title/%s', $title->getImdbId()));

        $url->appendNode(new Text($title->getTitle()));

        return $url;
    }

    /**
     * @return array<Tag>
     */
    private function getGenreTags(Title $title): array
    {
        $genres = explode(', ', $title->getGenre());

        return array_map(fn (string $genre) => (new Tag())->appendNode(new Text($genre)), $genres);
    }

    /**
     * @return array<Node>
     */
    private function getRatings(Ratings $ratings): array
    {
        $nodes = [];

        if ($ratings->getImdb() !== null) {
            $nodes[] = new Separator();
            $nodes[] = new Text(sprintf('🎥 %s', $ratings->getImdb()->getValue()));
        }

        if ($ratings->getRottenTomatoes() !== null) {
            $nodes[] = new Separator();
            $nodes[] = new Text(sprintf('🍅 %s', $ratings->getRottenTomatoes()->getValue()));
        }

        return $nodes;
    }
}
