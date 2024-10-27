<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\DownloadPage;
use App\Jobs\Traits\ReleasesLinkOnError;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;

class DownloadBulletinIndex extends DownloadPage
{
    use ReleasesLinkOnError;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Link $link
    ) {
        $params = new GetLinkParams($link);

        if (! $params->year || ! $params->bulletin) {
            throw new \InvalidArgumentException("Incorrect link for downloading a bulletin's index");
        }

        $url = BocUrl::BulletinIndex->value;
        $url = str_replace('{year}', $params->year, $url);
        $url = str_replace('{bulletin}', $params->bulletin, $url);

        parent::__construct(
            url: $url,
            name: BocUrl::BulletinIndex->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractLinksFromBulletinIndex::dispatch($page);
    }
}
