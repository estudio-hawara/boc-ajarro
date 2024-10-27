<?php

namespace App\Actions;

use App\Models\Link;
use Illuminate\Support\Facades\Storage;
use Spatie\Robots\RobotsTxt;

class IsLinkAllowed
{
    public readonly bool $allowed;

    public function __construct(Link $link)
    {
        $this->allowed = RobotsTxt::readFrom(
            Storage::disk('local')->path('robots.txt')
        )->allows($link->url);
    }
}
