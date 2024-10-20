<?php

namespace App\Actions;

use App\Models\Page;
use voku\helper\HtmlDomParser;

class GetParsedDom
{
    public readonly ?Page $page;

    public readonly ?HtmlDomParser $dom;

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
            $this->dom = HtmlDomParser::str_get_html($this->page->getContent());
            $this->error = null;
        } else {
            $this->dom = null;
        }
    }
}
