<?php

namespace App\Models\LinkModel;

use App\Http\BocUrl;
use Illuminate\Database\Eloquent\Builder;

trait Filters
{
    /**
     * Links that have already been downloaded.
     */
    public function scopeDownloaded(Builder $link): void
    {
        $link->whereHas('lastDownload');
    }

    /**
     * Links that have not been downloaded yet.
     */
    public function scopeNotDownloaded(Builder $link): void
    {
        $link->whereNot(fn ($query) => $query->downloaded());
    }

    /**
     * Links found on a certain page.
     */
    public function scopeFoundIn(Builder $link, BocUrl $bocUrl): void
    {
        $link->whereHas(
            'page',
            fn (Builder $page) => $page->where('name', '=', $bocUrl->name)
        );
    }

    /**
     * Links where the download already started.
     */
    public function scopeDownloadStarted(Builder $link): void
    {
        $link->whereNotNull('download_started_at');
    }

    /**
     * Links where the download has not been started yet.
     */
    public function scopeNotDownloadStarted(Builder $link): void
    {
        $link->whereNot(fn ($query) => $query->scopeDownloadStarted());
    }

    /**
     * Links that have been disallowed by the robots.txt policies.
     */
    public function scopeDisallowed(Builder $link): void
    {
        $link->whereNotNull('disallowed_at');
    }

    /**
     * Links that have not been disallowed by the robots.txt policies.
     */
    public function scopeNotDisallowed(Builder $link): void
    {
        $link->whereNot(fn ($query) => $query->disallowed());
    }
}
