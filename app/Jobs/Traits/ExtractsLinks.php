<?php

namespace App\Jobs\Traits;

use App\Actions\GetParsedDom;
use App\Http\BocUrl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

trait ExtractsLinks
{
    protected ?BocUrl $type = null;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $extractions = Queue::size('extract');

        if ($extractions >= config('app.max_extractions', 250)) {
            $this->logAndDelete("The maximum number of $extractions extractions was reached, so a scheduled download job was ignored.");

            return;
        }

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
