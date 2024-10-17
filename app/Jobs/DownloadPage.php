<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class DownloadPage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $url)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::get($this->url);

        if (! $response->successful()) {
            $this->fail(
                (new \DateTime)->format('yyyy-mm-dd hh:ii:ss').
                "Could't download this page: {$this->url}."
            );
        }

        Page::create([
            'url' => $this->url,
            'content' => $response->body(),
        ]);
    }
}
