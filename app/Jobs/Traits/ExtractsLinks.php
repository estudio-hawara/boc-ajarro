<?php

namespace App\Jobs\Traits;

use App\Actions\GetParsedDom;
use App\Http\BocUrl;
use Illuminate\Support\Facades\DB;

trait ExtractsLinks
{
    protected ?BocUrl $type = null;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get and parse the page DOM
        $parsing = new GetParsedDom($this->page);

        if ($parsing->error) {
            $this->logAndDelete($parsing->error);

            return;
        }

        // Check if the page already has parsed links
        $existingLinkCount = $parsing->page->links->count();

        if ($existingLinkCount && ! $this->recreate) {
            $this->logAndDelete("The page with id {$this->page->id} already has links.");

            return;
        }

        // Extract the links
        $links = $this->chosenLinks($parsing->dom->find('a'), $parsing->page->url);
        $type = $this?->type?->name ?? '';

        // ... and store them
        DB::transaction(function () use ($existingLinkCount, $parsing, $links, $type) {
            if ($existingLinkCount && $this->recreate) {
                $parsing->page->links()->delete();
            }

            $parsing->page->links()->createMany(
                $links->map(fn ($link) => [
                    'type' => $type,
                    'url' => $link,
                ])
            );
        });
    }
}