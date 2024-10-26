<?php

namespace App\Jobs;

use App\Actions\GetParsedDom;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExtractPageLinks extends AbstractJob
{
    protected ?string $type = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Page $page,
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
        $parsing = new GetParsedDom($this->page);

        if ($parsing->error) {
            $this->logAndFail($parsing->error);

            return;
        }

        // Check if the page already has parsed links
        $existingLinkCount = $parsing->page->links->count();

        if ($existingLinkCount && ! $this->recreate) {
            $this->logAndFail("The page with id {$this->page->id} already has links.");

            return;
        }

        // Extract the links
        $links = $this->chosenLinks($parsing->dom->find('a'), $parsing->page->url);
        $type = $this->type;

        // ... and store them
        DB::transaction(function () use ($existingLinkCount, $parsing, $links, $type) {
            if ($existingLinkCount && $this->recreate) {
                $parsing->page->links()->delete();
            }

            $parsing->page->links()->createMany(
                $links->map(fn ($link) => [
                    'type' => $type,
                    'url' => $link,
                ])
            );
        });
    }

    /**
     * Function that filters the links that will be kept from this page.
     */
    protected function chosenLinks(array $allLinks, string $pageUrl): Collection
    {
        $links = [];

        foreach ($allLinks as $link) {
            $url = urljoin($this->root, $pageUrl, $link?->href ?? '');

            if (in_array($url, $links)) {
                continue;
            }

            $links[] = $url;
        }

        return collect($links)->sort();
    }
}
