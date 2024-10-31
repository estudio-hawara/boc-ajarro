<?php

namespace App\Jobs\Traits;

use Illuminate\Support\Collection;

trait FiltersLinks
{
    /**
     * Get the root of the site.
     */
    protected function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Decide if a link should be kept.
     */
    protected function chooseLink(string $url): bool
    {
        return true;
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(array $allLinks, string $pageUrl): Collection
    {
        $links = [];

        foreach ($allLinks as $link) {
            $url = urljoin($this->getRoot(), $pageUrl, $link?->href ?? '');

            if (! $this->chooseLink($url)) {
                continue;
            }

            if (in_array($url, $links)) {
                continue;
            }

            $links[] = $url;
        }

        return collect($links)->sort();
    }
}
