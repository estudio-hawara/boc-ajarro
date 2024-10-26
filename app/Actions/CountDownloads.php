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

    public readonly int $disallowedYearIndex;

    public readonly int $disallowedBulletinIndex;

    public readonly int $disallowedBulletinArticle;

    public function __construct()
    {
        $this->totalYearIndex = Link::ofType(BocUrl::YearIndex)->count();
        $this->totalBulletinIndex = Link::ofType(BocUrl::BulletinIndex)->count();
        $this->totalBulletinArticle = Link::ofType(BocUrl::BulletinArticle)->count();

        $this->missingYearIndex = Link::ofType(BocUrl::YearIndex)->notDownloaded()->notDisallowed()->count();
        $this->missingBulletinIndex = Link::ofType(BocUrl::BulletinIndex)->notDownloaded()->notDisallowed()->count();
        $this->missingBulletinArticle = Link::ofType(BocUrl::BulletinArticle)->notDownloaded()->notDisallowed()->count();

        $this->disallowedYearIndex = Link::ofType(BocUrl::YearIndex)->disallowed()->count();
        $this->disallowedBulletinIndex = Link::ofType(BocUrl::BulletinIndex)->disallowed()->count();
        $this->disallowedBulletinArticle = Link::ofType(BocUrl::BulletinArticle)->disallowed()->count();
    }
}
