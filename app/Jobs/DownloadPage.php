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
    public function __construct(
        protected string $url,
        protected string $name,
    ) {
        //
    }

    /**
     * Get the URL of the page downloaded.
     */
    public function getUrl(): string
    {
        return $this->url;
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

            return;
        }

        $page = [
            'name' => $this->name,
            'created_at' => \Carbon\Carbon::now(),
        ];

        $previousPage = Page::select('id', 'content')
            ->whereNotNull('content')
            ->where('name', '=', $this->name)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $previousPage || $previousPage['content'] != $response->body()) {
            $page['content'] = $response->body();
        } else {
            $page['shared_content_with_page_id'] = $previousPage['id'];
        }

        Page::create($page);
    }
}
