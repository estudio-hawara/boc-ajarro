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

class DownloadBulletinArticle extends AbstractJob
{
    use ChecksDownloadQueueSize;
    use DownloadsContent;
    use ReleasesLinkOnError;

    protected string $url;

    protected string $name = BocUrl::BulletinArticle->name;

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

        if (! $params->year || ! $params->bulletin || ! $params->article) {
            $this->logAndDelete("Incorrect link for downloading a bulletin's article");

            return;
        }

        $url = BocUrl::BulletinArticle->value;
        $url = str_replace('{year}', $params->year, $url);
        $url = str_replace('{bulletin}', $params->bulletin, $url);
        $url = str_replace('{article}', $params->article, $url);

        $this->url = $url;
        $this->onQueue('download');
    }

    // @codeCoverageIgnoreStart

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        //
    }

    // @codeCoverageIgnoreEnd
}
