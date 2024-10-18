<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = ['created_at'];

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'shared_content_with_page_id', 'content'];

    /**
     * The page content was already found in another download.
     */
    public function pageWithSharedContent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'shared_content_with_page_id');
    }
}
