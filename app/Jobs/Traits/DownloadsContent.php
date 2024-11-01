<?php

namespace App\Jobs\Traits;

use App\Models\Page;
use Illuminate\Support\Facades\Http;

trait DownloadsContent
{
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
        $response = Http::get($this->getUrl());

        if (! $response->successful()) {
            $this->handleError();

            return;
        }

        $page = [
            'name' => $this->name,
            'url' => $this->getUrl(),
            'created_at' => \Carbon\Carbon::now(),
        ];

        $previousPage = Page::select('id', 'content')
            ->whereNotNull('content')
            ->whereName($this->name)
            ->whereUrl($this->getUrl())
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
}
