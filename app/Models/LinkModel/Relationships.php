<?php

namespace App\Models\LinkModel;

use App\Models\Page;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait Relationships
{
    // @codeCoverageIgnoreStart

    /**
     * The page where the link was found.
     */
    public function page(): HasOne
    {
        return $this->hasOne(Page::class, 'id', 'page_id');
    }

    /**
     * The last download of this link.
     */
    public function lastDownload(): HasOne
    {
        return $this->hasOne(Page::class, 'url', 'url')
            ->orderBy('created_at', 'desc')
            ->limit(1);
    }

    // @codeCoverageIgnoreEnd
}
