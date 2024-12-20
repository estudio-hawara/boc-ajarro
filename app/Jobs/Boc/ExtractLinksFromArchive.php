<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\ChecksExtractionQueueSize;
use App\Jobs\Traits\ExtractsLinks;
use App\Jobs\Traits\FiltersLinks;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class ExtractLinksFromArchive extends AbstractJob
{
    use ChecksExtractionQueueSize;
    use ExtractsLinks;
    use FiltersLinks;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Page $page,
        protected bool $recreate = false,
    ) {
        if ($this->maxQueueSizeExceeded()) {
            $this->logAndDelete('The maximum number of extractions was reached, so an extraction job was ignored.');

            return;
        }

        if (! $page->exists()) {
            $this->logAndDelete("The page with id {$page->id} does not exist.");

            return;
        }

        if ($page->exists() && $page->name != BocUrl::Archive->name) {
            $this->logAndDelete("The page with id {$page->id} is not an archive page.");

            return;
        }

        $this->type = BocUrl::YearIndex;
        $this->onQueue('extract');
    }

    /**
     * Get the root of the site.
     */
    protected function getRoot(): string
    {
        return BocUrl::Root->value;
    }

    /**
     * Decide if a link should be kept.
     */
    protected function chooseLink(string $url): bool
    {
        return preg_match($this->type->pattern(), rtrim($url, '/'));
    }
}
