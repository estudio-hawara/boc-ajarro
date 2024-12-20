<?php

namespace App\Jobs\Boc;

use App\Actions\IsLinkAllowed;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\AbandonsQueueOnError;
use App\Jobs\Traits\ChecksDownloadQueueSize;
use App\Models\Link;
use Illuminate\Support\Facades\DB;

class FollowLinksFoundInBulletinIndex extends AbstractJob
{
    use AbandonsQueueOnError;
    use ChecksDownloadQueueSize;

    public function __construct()
    {
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
            $links = Link::foundIn(BocUrl::BulletinIndex)
                ->notDownloaded()
                ->notDownloadStarted()
                ->notDisallowed()
                ->orderBy('created_at')
                ->limit($this->getQueueCapacity())
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

        DownloadBulletinArticle::dispatch($link);
    }
}
