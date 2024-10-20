<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

abstract class AbstractJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    /**
     * Log the error message and mark the job as failed.
     */
    public function logAndFail(string $message)
    {
        Log::warning($message);
        $now = (new \DateTime)->format('yyyy-mm-dd hh:ii:ss');
        $this->fail("$now $message");
    }
}
