<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\DownloadPage;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class DownloadYearIndex extends DownloadPage
{
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
        ExtractBulletinIndexLinksFromYearIndex::dispatch($page);
    }

    /**
     * Handle an error during the download.
     */
    protected function handleError(): void
    {
        $this->link->download_started_at = null;
        $this->link->save();

        parent::handleError();
    }
}
