<?php

namespace App\Jobs\Boc;

use App\Actions\IsLinkAllowed;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\AbandonsQueueOnError;
use App\Jobs\Traits\ChecksDownloadQueueSize;
use App\Models\Link;
use Illuminate\Support\Facades\DB;

class FollowLinksFoundInYearIndex extends AbstractJob
{
    use ChecksDownloadQueueSize;
    use AbandonsQueueOnError;

    public function __construct(
        protected int $limit = 50
    ) {
        if ($this->maxQueueSizeExceeded()) {
            $this->logAndDelete('The maximum number of downloads was reached, so a download job was ignored.');

            return;
        }

        $this->onQueue('download');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->abandoned) {
            return;
        }

        $links = collect();

        DB::transaction(function () use (&$links) {
            $links = Link::foundIn(BocUrl::YearIndex)
                ->notDownloaded()
                ->notDownloadStarted()
                ->notDisallowed()
                ->orderBy('created_at')
                ->limit($this->limit)
                ->get();

            Link::whereIn('id', $links->pluck('id'))
                ->update(['download_started_at' => \Carbon\Carbon::now()]);
        });

        if (! $links->count()) {
            return;
        }

        foreach ($links as $link) {
            $this->process($link);
        }
    }

    private function process(Link $link): void
    {
        if (! (new IsLinkAllowed($link))->allowed) {
            $link->download_started_at = null;
            $link->disallowed_at = \Carbon\Carbon::now();
            $link->save();

            return;
        }

        DownloadBulletinIndex::dispatch($link);
    }
}
