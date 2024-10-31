<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\ExtractPageLinks;
use App\Jobs\Traits\ExtractsLinks;
use App\Jobs\Traits\FiltersBocLinks;
use App\Jobs\Traits\FiltersLinks;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Collection;

class ExtractLinksFromYearIndex extends AbstractJob
{
    use ExtractsLinks;
    use FiltersLinks;
    use ReleasesLinkOnError;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Page $page,
        protected bool $recreate = false,
    ) {
        $this->type = BocUrl::BulletinIndex;

        if (! $page->exists()) {
            $this->logAndDelete("The page with id {$page->id} does not exist.");
        }

        if ($page->exists() && $page->name != BocUrl::YearIndex->name) {
            $this->logAndDelete("The page with id {$page->id} is not a year index.");
        }

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
