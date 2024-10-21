<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
use Illuminate\Support\Collection;
use voku\helper\SimpleHtmlDomNodeInterface;

class ExtractBulletinIndexLinks extends ExtractPageLinks
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $pageId,
        protected bool $recreate = false,
    ) {
        $page = Page::find($pageId);

        if (! $page) {
            $this->logAndFail("The page with id $pageId does not exist.");

            return;
        }

        if ($page->name != BocUrl::BulletinIndex->name) {
            $this->logAndFail("The page with id $pageId is not a bulletin index.");

            return;
        }

        parent::__construct(
            pageId: $pageId,
            root: BocUrl::Root->value,
            recreate: $recreate
        );
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(SimpleHtmlDomNodeInterface $node, string $pageUrl): Collection
    {
        $links = [];

        foreach ($node->findMulti('a') as $link) {
            $url = urljoin(BocUrl::Root->value, $pageUrl, $link->href);

            if (! preg_match('/https:\/\/www.gobiernodecanarias.org\/boc\/\d{4,}\/\d{3,}\/\d{3,}\.html$/', rtrim($url, '/'))) {
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
