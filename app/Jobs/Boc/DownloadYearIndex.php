<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

class DownloadYearIndex extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct(protected string $year)
    {
        parent::__construct(
            url: str_replace('{year}', $year, BocUrl::YearIndex->value),
            name: BocUrl::YearIndex->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(int $pageId): void
    {
        ExtractYearIndexLinks::dispatch(
            pageId: $pageId,
            root: $this->root,
        );
    }
}
