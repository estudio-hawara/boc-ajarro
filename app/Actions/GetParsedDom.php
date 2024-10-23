<?php

namespace App\Actions;

use App\Models\Page;
use DiDom\Document;

class GetParsedDom
{
    public readonly ?Page $page;

    public readonly ?Document $dom;

    public readonly ?string $error;

    public function __construct(int $pageId)
    {
        $this->page = Page::find($pageId);

        if (! $this->page) {
            $this->error = "Could't find a page with id: $pageId.";
        }

        if ($this->page && ! $this->page->content) {
            $this->error = "The page with id: $pageId has a content that was previously found, so it will be ignored.";
        }

        if (! isset($this->error)) {
            $document = new Document();
            $document->loadHtml($this->page->getContent());

            $this->dom = $document;
            $this->error = null;
        } else {
            $this->dom = null;
        }
    }
}
