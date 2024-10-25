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

        if (! $robots->mayIndex($link->url)) {
            $this->allowed = false;

            $link->disallowed_at = \Carbon\Carbon::now();
            $link->save();

            return;
        }

        $this->allowed = true;
    }
}