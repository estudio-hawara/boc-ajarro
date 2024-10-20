<?php

namespace App\Models;

use App\Models\PageModel\Relationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    use Relationships;

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

    /**
     * Return the page content.
     */
    public function getContent(): string
    {
        return $this?->content ?? $this->pageWithSharedContent->content;
    }
}
