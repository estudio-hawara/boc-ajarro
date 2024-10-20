<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

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
    protected function extractLinks(int $pageId): void
    {
        ExtractArchiveLinks::dispatch(
            pageId: $pageId,
            root: $this->root,
        );
    }
}
