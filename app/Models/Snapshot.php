<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = ['created_at'];

    const UPDATED_AT = null;

    protected $fillable = [
        'total_year_index',
        'total_bulletin_index',
        'total_bulletin_article',
        'missing_year_index',
        'missing_bulletin_index',
        'missing_bulletin_article',
        'disallowed_year_index',
        'disallowed_bulletin_index',
        'disallowed_bulletin_article',
    ];
}
