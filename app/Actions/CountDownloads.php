<?php

namespace App\Actions;

use App\Http\BocUrl;
use App\Models\Link;

class CountDownloads
{
    public readonly int $totalYearIndex;

    public readonly int $totalBulletinIndex;

    public readonly int $totalBulletinArticle;

    public readonly int $missingYearIndex;

    public readonly int $missingBulletinIndex;

    public readonly int $missingBulletinArticle;

    public function __construct()
    {
        $this->totalYearIndex = Link::foundIn(BocUrl::Archive)->count();
        $this->totalBulletinIndex = Link::foundIn(BocUrl::YearIndex)->count();
        $this->totalBulletinArticle = Link::foundIn(BocUrl::BulletinIndex)->count();

        $this->missingYearIndex = Link::foundIn(BocUrl::Archive)->notDownloaded()->count();
        $this->missingBulletinIndex = Link::foundIn(BocUrl::YearIndex)->notDownloaded()->count();
        $this->missingBulletinArticle = Link::foundIn(BocUrl::BulletinIndex)->notDownloaded()->count();
    }
}
