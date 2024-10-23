<?php

namespace App\Actions;

use App\Http\BocUrl;
use App\Models\Link;

class GetLinkParams
{
    public readonly ?string $year;
    public readonly ?string $bulletin;
    public readonly ?string $article;

    public function __construct(Link $link)
    {
        $foundIn = BocUrl::{$link->page->name};
        $contains = $foundIn->contains();

        $matches = [];

        if ($contains) {
            $pattern = BocUrl::{$contains->name}->pattern();
            preg_match($pattern, $link->url, $matches);
        }

        $this->year = array_key_exists('year', $matches) ? $matches['year'] : null;
        $this->bulletin = array_key_exists('bulletin', $matches) ? $matches['bulletin'] : null;
        $this->article = array_key_exists('article', $matches) ? $matches['article'] : null;
    }
}
