<?php

namespace App\Jobs\Boc;

use App\Actions\IsLinkAllowed;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Models\Link;
use Illuminate\Support\Facades\DB;

class FollowLinksFoundInBulletinIndex extends AbstractJob
{
    protected int $limit = 5;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $links = collect();

        DB::transaction(function () use (&$links) {
            $links = Link::foundIn(BocUrl::BulletinIndex)
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

        DownloadBulletinArticle::dispatch($link);
    }
}
