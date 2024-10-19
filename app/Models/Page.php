<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

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
    protected $fillable = ['name', 'url', 'shared_content_with_page_id', 'content'];

    // @codeCoverageIgnoreStart

    /**
     * The page content was already found in another download.
     */
    public function pageWithSharedContent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'shared_content_with_page_id');
    }

    // @codeCoverageIgnoreEnd

    /**
     * Return the page content.
     */
    public function getContent(): string
    {
        return $this?->content ?? $this->pageWithSharedContent->content;
    }
}
