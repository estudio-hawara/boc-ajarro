<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

class DownloadBulletinIndex extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct(protected string $year, protected string $bulletin)
    {
        $url = BocUrl::BulletinIndex->value;
        $url = str_replace('{year}', $year, $url);
        $url = str_replace('{bulletin}', $bulletin, $url);

        parent::__construct(
            url: $url,
            name: BocUrl::BulletinIndex->name,
            root: BocUrl::Root->value
        );
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(int $pageId): void
    {
        ExtractBulletinIndexLinks::dispatch($pageId);
    }
}
