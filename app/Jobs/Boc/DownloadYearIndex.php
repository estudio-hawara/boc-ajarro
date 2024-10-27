<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\DownloadPage;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class DownloadYearIndex extends DownloadPage
{
    use ReleasesLinkOnError;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Link $link
    ) {
        $params = new GetLinkParams($link);

        if (! $params->year) {
            throw new \InvalidArgumentException("Incorrect link for downloading a year's index");
        }

        $url = str_replace('{year}', $params->year, BocUrl::YearIndex->value);

        parent::__construct(
            url: $url,
            name: BocUrl::YearIndex->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractLinksFromYearIndex::dispatch($page);
    }
}
