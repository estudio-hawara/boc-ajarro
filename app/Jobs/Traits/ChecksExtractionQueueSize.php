<?php

namespace App\Jobs\Traits;

use Illuminate\Support\Facades\Queue;

trait ChecksExtractionQueueSize
{
    /**
     * Check the queue size.
     */
    public function maxQueueSizeExceeded(): bool
    {
        $extractions = Queue::size('extract');
        $maxExtractions = config('app.max_extractions', 250);

        return $extractions >= $maxExtractions;
    }
}
