<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\ChecksDownloadQueueSize;
use App\Jobs\Traits\DownloadsContent;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class DownloadRobots extends AbstractJob
{
    use ChecksDownloadQueueSize;
    use DownloadsContent;
    use ReleasesLinkOnError;

    protected string $url = BocUrl::Robots->value;

    protected string $name = BocUrl::Robots->name;

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
        $content = mb_convert_encoding($page->content, 'UTF-8', 'UTF-8');

        Storage::disk('local')
            ->put('robots.txt', $content);
    }
}
