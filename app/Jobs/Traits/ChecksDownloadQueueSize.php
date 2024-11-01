<?php

namespace App\Jobs\Traits;

use Illuminate\Support\Facades\Queue;

trait ChecksDownloadQueueSize
{
    /**
     * Check the queue size.
     */
    public function maxQueueSizeExceeded(): bool
    {
        $downloads = Queue::size('download');
        $maxDownloads = config('app.max_downloads', 250);

        return $downloads >= $maxDownloads;
    }
}
