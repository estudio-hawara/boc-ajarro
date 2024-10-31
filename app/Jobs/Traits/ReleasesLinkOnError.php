<?php

namespace App\Jobs\Traits;

trait ReleasesLinkOnError
{
    /**
     * Handle an error during the download.
     */
    protected function handleError(): void
    {
        $this->link->download_started_at = null;
        $this->link->save();

        $this->logAndDelete("Could't download this page: {$this->link->url}.");
    }
}
