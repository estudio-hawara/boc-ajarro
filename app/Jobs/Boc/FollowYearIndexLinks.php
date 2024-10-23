<?php

namespace App\Jobs\Boc;

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Jobs\AbstractJob;
use App\Models\Link;
use Illuminate\Support\Facades\Storage;
use Spatie\Robots\Robots;

class FollowYearIndexLinks extends AbstractJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $link = Link::foundIn(BocUrl::YearIndex)
            ->notDownloaded()
            ->notDisallowed()
            ->orderBy('created_at')
            ->limit(1)
            ->first();

        if (! $link) {
            return;
        }

        $robots = Robots::create()->withTxt(
            Storage::disk('local')->path('robots.txt'),
        );

        if (! $robots->mayIndex($link->url)) {
            $link->disallowed_at = \Carbon\Carbon::now();
            $link->save();

            return;
        }

        $params = new GetLinkParams($link);
        DownloadBulletinIndex::dispatch($params->year, $params->bulletin);
    }
}
