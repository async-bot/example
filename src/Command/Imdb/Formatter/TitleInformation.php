<?php declare(strict_types=1);

namespace AsyncBot\Example\Command\Imdb\Formatter;

use AsyncBot\Plugin\Imdb\ValueObject\Result\Ratings;
use AsyncBot\Plugin\Imdb\ValueObject\Result\Title;

final class TitleInformation
{
    public function format(Title $title): string
    {
        return sprintf(
            '%s (%d) | %s | %s %s',
            $this->formatTitle($title),
            $title->getYear(),
            $title->getRunTime(),
            $this->getGenresAsTags($title->getGenre()),
            $this->formatRatings($title->getRatings()),
        );
    }

    private function formatTitle(Title $title): string
    {
        $url = $title->getWebsite() ?? sprintf('https://www.imdb.com/title/%s', $title->getImdbId());

        return sprintf('[**%s**](%s)', $title->getTitle(), $url);
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
