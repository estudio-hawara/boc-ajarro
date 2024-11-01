<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\AbandonsQueueOnError;
use App\Jobs\Traits\ChecksDownloadQueueSize;
use App\Jobs\Traits\DownloadsContent;
use App\Models\Page;

class DownloadArchive extends AbstractJob
{
    use AbandonsQueueOnError;
    use ChecksDownloadQueueSize;
    use DownloadsContent;

    protected string $url = BocUrl::Archive->value;

    protected string $name = BocUrl::Archive->name;

    protected string $root = BocUrl::Root->value;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if ($this->maxQueueSizeExceeded()) {
            $this->logAndDelete('The maximum number of downloads was reached, so a download job was ignored.');

            return;
        }

        $this->onQueue('download');
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractLinksFromArchive::dispatch($page);
    }
}
