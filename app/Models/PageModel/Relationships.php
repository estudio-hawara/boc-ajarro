<?php

namespace App\Models\PageModel;

use App\Models\Link;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait Relationships
{
    // @codeCoverageIgnoreStart

    /**
     * The page content was already found in another download.
     */
    public function pageWithSharedContent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'shared_content_with_page_id');
    }

    /**
     * Links that where found in this page.
     */
    public function links(): HasMany
    {
        if ($this->pageWithSharedContent) {
            return $this->pageWithSharedContent->hasMany(Link::class);
        }

        return $this->hasMany(Link::class);
    }

    // @codeCoverageIgnoreEnd
}
