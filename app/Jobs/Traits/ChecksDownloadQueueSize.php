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
        return $this->getQueueCapacity() <= 0;
    }

    /**
     * Get the current queue capacity.
     */
    public function getQueueCapacity(): int
    {
        $downloads = Queue::size('download');
        $maxDownloads = config('app.max_downloads', 250);

        return $maxDownloads - $downloads;
    }
}
