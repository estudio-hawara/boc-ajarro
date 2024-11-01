<?php

namespace App\Jobs\Boc;

use App\Actions\Boc\GetTextContent;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\ChecksExtractionQueueSize;
use App\Models\Page;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Facades\Queue;

class ExtractContentFromArticle extends AbstractJob
{
    use ChecksExtractionQueueSize;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        protected Page $page,
        protected bool $recreate = false,
    ) {
        if ($this->maxQueueSizeExceeded()) {
            $this->logAndDelete('The maximum number of extractions was reached, so an extraction job was ignored.');

            return;
        }

        if (! $page->exists()) {
            $this->logAndDelete("The page with id {$page->id} does not exist.");

            return;
        }

        if ($page->exists() && $page->name != BocUrl::BulletinArticle->name) {
            $this->logAndDelete("The page with id {$page->id} is not a bulletin article.");

            return;
        }

        $this->onQueue('extract');
    }

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
        $content = new GetTextContent($this->page);

        if ($content->error) {
            $this->logAndDelete($content->error);

            return;
        }

        // TODO: Store `$content->text` somewhere.
    }
}
