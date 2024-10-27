<?php

namespace App\Jobs;

use App\Jobs\Traits\AbandonsQueueOnError;
use App\Jobs\Traits\DownloadsContent;
use App\Models\Page;

class DownloadPage extends AbstractJob
{
    use DownloadsContent;
    use AbandonsQueueOnError;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $url,
        protected string $name,
        protected ?string $root = null
    ) {
        if (! $root) {
            $this->root = $url;
        }
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractPageLinks::dispatch(
            page: $page,
            root: $this->root,
        );
    }
}
