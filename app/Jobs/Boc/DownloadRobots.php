<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class DownloadRobots extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        parent::__construct(
            url: BocUrl::Robots->value,
            name: BocUrl::Robots->name,
            root: BocUrl::Root->value
        );
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
