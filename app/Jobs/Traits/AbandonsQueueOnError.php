<?php

namespace App\Jobs\Traits;

trait AbandonsQueueOnError
{
    /**
     * Handle an error during the download.
     */
    protected function handleError(): void
    {
        $this->logAndDelete("Could't download this page: {$this->url}.");
    }
}