<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

class DownloadYearIndex extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $year
    ) {
        $url = str_replace('{year}', $year, BocUrl::YearIndex->value);

        parent::__construct(
            url: $url,
            name: BocUrl::YearIndex->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(int $pageId): void
    {
        ExtractYearIndexLinks::dispatch($pageId);
    }
}
