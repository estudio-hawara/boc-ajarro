<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Queue\Attributes\WithoutRelations;

class ExtractBulletinIndexLinks extends ExtractPageLinks
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Page $page,
        protected bool $recreate = false,
    ) {
        if (! $page) {
            $this->logAndFail("The page with id {$page->id} does not exist.");

            return;
        }

        if ($page->name != BocUrl::BulletinIndex->name) {
            $this->logAndFail("The page with id {$page->id} is not a bulletin index.");

            return;
        }

        parent::__construct(
            page: $page,
            root: BocUrl::Root->value,
            recreate: $recreate
        );
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(array $allLinks, string $pageUrl): Collection
    {
        $links = [];

        foreach ($allLinks as $link) {
            $url = urljoin(BocUrl::Root->value, $pageUrl, $link?->href ?? '');

            if (! preg_match(BocUrl::BulletinArticle->pattern(), rtrim($url, '/'))) {
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
