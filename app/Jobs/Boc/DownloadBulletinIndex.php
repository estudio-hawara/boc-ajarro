<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\ChecksDownloadQueueSize;
use App\Jobs\Traits\DownloadsContent;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class DownloadBulletinIndex extends AbstractJob
{
    use ChecksDownloadQueueSize;
    use DownloadsContent;
    use ReleasesLinkOnError;

    protected string $url;

    protected string $name = BocUrl::BulletinIndex->name;

    protected string $root = BocUrl::Root->value;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Link $link
    ) {
        if ($this->maxQueueSizeExceeded()) {
            $this->logAndDelete('The maximum number of downloads was reached, so a download job was ignored.');

            return;
        }

        $params = new GetLinkParams($link);

        if (! $params->year || ! $params->bulletin) {
            throw new \InvalidArgumentException("Incorrect link for downloading a bulletin's index");
        }

        $url = BocUrl::BulletinIndex->value;
        $url = str_replace('{year}', $params->year, $url);
        $url = str_replace('{bulletin}', $params->bulletin, $url);

        $this->url = $url;
        $this->onQueue('download');
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractLinksFromBulletinIndex::dispatch($page);
    }
}
