<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Collection;

class ExtractLinksFromArchive extends ExtractPageLinks
{
    protected ?string $type = BocUrl::YearIndex->name;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Page $page,
        protected bool $recreate = false,
    ) {
        parent::__construct(
            page: $page,
            root: BocUrl::Root->value,
            recreate: $recreate
        );

        if (! $page->exists()) {
            $this->logAndDelete("The page with id {$page->id} does not exist.");
        }

        if ($page->exists() && $page->name != BocUrl::Archive->name) {
            $this->logAndDelete("The page with id {$page->id} is not an archive page.");
        }
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(array $allLinks, string $pageUrl): Collection
    {
        $links = [];

        foreach ($allLinks as $link) {
            $url = urljoin(BocUrl::Root->value, $pageUrl, $link?->href ?? '');

            if (! preg_match(BocUrl::YearIndex->pattern(), rtrim($url, '/'))) {
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
