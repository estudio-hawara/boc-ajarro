<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

class DownloadBulletinArchive extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $year,
        protected string $bulletin,
        protected string $article
    ) {
        $url = BocUrl::BulletinArticle->value;
        $url = str_replace('{year}', $year, $url);
        $url = str_replace('{bulletin}', $bulletin, $url);
        $url = str_replace('{article}', $article, $url);

        parent::__construct(
            url: $url,
            name: BocUrl::BulletinArticle->name,
            root: BocUrl::Root->value
        );
    }

    // @codeCoverageIgnoreStart

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(int $pageId): void
    {
        //
    }

    // @codeCoverageIgnoreEnd
}
