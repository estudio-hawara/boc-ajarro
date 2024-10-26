<?php

namespace App\Actions;

use App\Models\Link;
use Illuminate\Support\Facades\Storage;
use Spatie\Robots\Robots;

class IsLinkAllowed
{
    public readonly bool $allowed;

    public function __construct(Link $link)
    {
        $robots = Robots::create()->withTxt(
            Storage::disk('local')->path('robots.txt'),
        );

        $this->allowed = $robots->mayIndex($link->url);
    }
}
