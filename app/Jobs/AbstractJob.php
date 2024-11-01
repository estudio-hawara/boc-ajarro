<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

abstract class AbstractJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    protected bool $abandoned = false;

    /**
     * Log the error message and mark the job as failed.
     */
    public function logAndDelete(string $message)
    {
        $now = (new \DateTime)->format('Y-m-d h:i:s');
        Log::warning("$now $message");
        $this->delete();
        $this->abandoned = true;
    }
}
