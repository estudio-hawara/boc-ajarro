<?php

namespace App\Jobs;

use App\Actions\CountDownloads;
use App\Models\Snapshot;

class TakeSnapshot extends AbstractJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $existing = Snapshot::whereDay('created_at', \Carbon\Carbon::today());
        $snapshot = $existing->count() ? $existing->first() : new Snapshot;

        $counts = new CountDownloads;
        $snapshot->total_year_index = $counts->totalYearIndex;
        $snapshot->total_bulletin_index = $counts->totalBulletinIndex;
        $snapshot->total_bulletin_article = $counts->totalBulletinArticle;
        $snapshot->missing_year_index = $counts->missingYearIndex;
        $snapshot->missing_bulletin_index = $counts->missingBulletinIndex;
        $snapshot->missing_bulletin_article = $counts->missingBulletinArticle;
        $snapshot->disallowed_year_index = $counts->disallowedYearIndex;
        $snapshot->disallowed_bulletin_index = $counts->disallowedBulletinIndex;
        $snapshot->disallowed_bulletin_article = $counts->disallowedBulletinArticle;
        $snapshot->save();
    }
}
