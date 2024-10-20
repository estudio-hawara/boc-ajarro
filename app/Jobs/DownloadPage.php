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

        $storeContent = ! $previousPage || $previousPage['content'] != $response->body();

        if ($storeContent) {
            $page['content'] = $response->body();
        } else {
            $page['shared_content_with_page_id'] = $previousPage['id'];
        }

        $created = Page::create($page);

        if ($storeContent) {
            $this->extractLinks($created->id, $this->root);
        }
    }

    /**
     * Extract the links of this page.
     */
    protected function extractLinks(int $pageId): void
    {
        ExtractPageLinks::dispatch(
            pageId: $pageId,
            root: $this->root,
        );
    }
}
