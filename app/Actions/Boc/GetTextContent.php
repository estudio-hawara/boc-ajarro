<?php

namespace App\Actions\Boc;

use App\Actions\GetParsedDom;
use App\Models\Page;
use DiDom\Element;

class GetTextContent
{
    public readonly ?string $text;

    public readonly ?string $error;

    public function __construct(Page $page)
    {
        $parsed = new GetParsedDom($page);
        $content = $parsed->dom->first('.conten');

        if (! $content) {
            $this->error = "The page with id {$page->id} does not look like a BOC article.";
        }

        $this->text = $this->extractText($content);
    }

    public function extractText(Element $content): string
    {
        $paragraphs = [];

        foreach ($this->filterElements($content) as $element) {
            $paragraph = $element->text();
            $paragraph = preg_replace('!\s+!', ' ', $paragraph);
            $paragraph = trim($paragraph);

            if ($paragraph) {
                $paragraphs[] = $paragraph;
            }
        }

        return implode("\n\n", $paragraphs);
    }

    public function filterElements(Element $content): \Iterator
    {
        foreach ($content->children() as $element) {
            if ($element->isElementNode() && $element->hasAttribute('id') && $element->id == 'listado_superior') {
                continue;
            }

            if ($element->isElementNode() && $element->matches('.espacio')) {
                continue;
            }

            yield $element;
        }
    }
}
