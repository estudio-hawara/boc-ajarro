<?php

namespace App\Jobs;

use App\Actions\GetParsedDom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use voku\helper\SimpleHtmlDomNodeInterface;

class ExtractPageLinks extends AbstractJob
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $pageId,
        protected string $root,
        protected bool $recreate = false,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get and parse the page DOM
        $parsing = new GetParsedDom($this->pageId);

        if ($parsing->error) {
            $this->logAndFail($parsing->error);

            return;
        }

        // Check if the page already has parsed links
        $existingLinkCount = $parsing->page->links->count();

        if ($existingLinkCount && ! $this->recreate) {
            $this->logAndFail("The page with id {$this->pageId} already has links.");

            return;
        }

        // Extract and store the links
        $links = $this->chosenLinks($parsing->dom->findMulti('a'), $parsing->page->url);

        DB::transaction(function () use ($existingLinkCount, $parsing, $links) {
            if ($existingLinkCount && $this->recreate) {
                $parsing->page->links()->delete();
            }

            $parsing->page->links()->createMany(
                $links->map(fn ($link) => ['url' => $link])
            );
        });
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(SimpleHtmlDomNodeInterface $node, string $pageUrl): Collection
    {
        $links = [];

        foreach ($node->findMulti('a') as $link) {
            $url = urljoin($this->root, $pageUrl, $link->href);

            if (in_array($url, $links)) {
                continue;
            }

            $links[] = $url;
        }

        return collect($links)->sort();
    }
}
