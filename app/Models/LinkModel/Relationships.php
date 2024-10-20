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

    // @codeCoverageIgnoreEnd
}