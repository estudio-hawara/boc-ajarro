<?php

namespace App\Actions;

use App\Models\Page;
use DiDom\Document;

class GetParsedDom
{
    public readonly ?Document $dom;

    public readonly ?string $error;

    public function __construct(public readonly Page $page)
    {
        if (! $page->exists()) {
            $this->error = "Could't find a page with id: {$page->id}.";
        }

        if ($page->exists() && ! $this->page->content) {
            $this->error = "The page with id: {$page->id} has a content that was previously found, so it will be ignored.";
        }

        if (! isset($this->error)) {
            $document = new Document;
            $document->loadHtml($this->page->getContent());

            $this->dom = $document;
            $this->error = null;
        } else {
            $this->dom = null;
        }
    }
}
