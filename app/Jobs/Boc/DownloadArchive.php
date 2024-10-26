<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;
use App\Models\Page;

class DownloadArchive extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        parent::__construct(
            url: BocUrl::Archive->value,
            name: BocUrl::Archive->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractYearIndexLinksFromArchive::dispatch($page);
    }
}
