<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Support\Facades\Http;

class DownloadPage extends AbstractJob
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $url,
        protected string $name,
        protected ?string $root = null
    ) {
        if (! $root) {
            $this->root = $url;
        }
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
            $this->logAndFail("Could't download this page: {$this->url}.");

            return;
        }

        $page = [
            'name' => $this->name,
            'url' => $this->url,
            'created_at' => \Carbon\Carbon::now(),
        ];

        $previousPage = Page::select('id', 'content')
            ->whereNotNull('content')
            ->whereName($this->name)
            ->whereUrl($this->url)
            ->orderBy('created_at', 'desc')
            ->first();

        $extractLinks = false;

        if (! $previousPage || $previousPage['content'] != $response->body()) {
            $page['content'] = $response->body();
            $extractLinks = true;
        } else {
            $page['shared_content_with_page_id'] = $previousPage['id'];
        }

        $created = Page::create($page);

        if ($extractLinks) {
            ExtractPageLinks::dispatch(
                pageId: $created->id,
                root: $this->root,
            );
        }
    }
}
