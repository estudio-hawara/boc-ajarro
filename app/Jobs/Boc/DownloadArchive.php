<?php

namespace App\Jobs\Boc;

use App\Http\BocUrl;
use App\Jobs\DownloadPage;

class DownloadArchive extends DownloadPage
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        parent::__construct(
            BocUrl::Archive->value,
            BocUrl::Archive->name
        );
    }
}
