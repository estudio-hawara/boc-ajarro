<?php

namespace App\Jobs\Boc;

use App\Actions\IsLinkAllowed;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\AbandonsQueueOnError;
use App\Models\Link;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class FollowLinksFoundInArchive extends AbstractJob
{
    use AbandonsQueueOnError;

    public function __construct(
        protected int $limit = 50
    ) {
        $this->onQueue('download');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $downloads = Queue::size('download');

        if ($downloads >= config('app.max_downloads', 16)) {
            $this->logAndDelete('The maximum number of downloads was reached, so a scheduled download job was ignored.');

            return;
        }

        $links = collect();

        DB::transaction(function () use (&$links) {
            $links = Link::foundIn(BocUrl::Archive)
                ->notDownloaded()
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

        DownloadYearIndex::dispatch($link);
    }
}
