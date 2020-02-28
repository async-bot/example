<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Formatter;

use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Node\Text;
use AsyncBot\Core\Message\Node\Url;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Ratings;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Title;

final class TitleInformation
{
    public function format(Title $title): Message
    {
        return (new Message())
            ->appendNode($this->formatTitle($title))
            ->appendNode(new Text(' '))
            ->appendNode(new Text(sprintf('(%d)', $title->getYear())))
            ->appendNode(new Text(' | '))
            ->appendNode(new Text($this->getGenresAsTags($title->getGenre())))
            ->appendNode(new Text($this->formatRatings($title->getRatings())))
        ;
    }

    private function formatTitle(Title $title): Url
    {
        $url = new Url($title->getWebsite() ?? sprintf('https://www.imdb.com/title/%s', $title->getImdbId()));

        $url->appendNode(new Text($title->getTitle()));

        return $url;
    }

    private function getGenresAsTags(string $genres): string
    {
        $genres = explode(', ', $genres);

        return implode(' ', array_map(fn (string $genre) => sprintf('[tag:%s]', $genre), $genres));
    }

    private function formatRatings(Ratings $ratings): string
    {
        $formattedRatings = '';

        if ($ratings->getImdb() !== null) {
            $formattedRatings .= sprintf(' | 🎥 %s', $ratings->getImdb()->getValue());
        }

        if ($ratings->getRottenTomatoes() !== null) {
            $formattedRatings .= sprintf(' | 🍅 %s', $ratings->getRottenTomatoes()->getValue());
        }

        return $formattedRatings;
    }
}
