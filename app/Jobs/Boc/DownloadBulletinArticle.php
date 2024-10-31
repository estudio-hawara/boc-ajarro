<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\DownloadPage;
use App\Jobs\Traits\DownloadsContent;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class DownloadBulletinArticle extends AbstractJob
{
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
        $params = new GetLinkParams($link);

        if (! $params->year || ! $params->bulletin || ! $params->article) {
            throw new \InvalidArgumentException("Incorrect link for downloading a bulletin's article");
        }

        $url = BocUrl::BulletinArticle->value;
        $url = str_replace('{year}', $params->year, $url);
        $url = str_replace('{bulletin}', $params->bulletin, $url);
        $url = str_replace('{article}', $params->article, $url);

        $this->url = $url;
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
