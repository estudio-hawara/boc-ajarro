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
            $this->handleError();

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

        $storeContent = ! $previousPage || $previousPage['content'] != $response->body();

        if ($storeContent) {
            $page['content'] = mb_convert_encoding($response->body(), 'UTF-8', 'UTF-8');
        } else {
            $page['shared_content_with_page_id'] = $previousPage['id'];
        }

        $created = Page::create($page);

        if ($storeContent) {
            $this->extractLinks($created);
        }
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(Page $page): void
    {
        ExtractPageLinks::dispatch(
            page: $page,
            root: $this->root,
        );
    }

    /**
     * Handle an error during the download.
     */
    protected function handleError(): void
    {
        $this->logAndFail("Could't download this page: {$this->url}.");
    }
}
