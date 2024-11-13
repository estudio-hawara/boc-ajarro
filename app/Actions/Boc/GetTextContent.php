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
            $paragraph = $this->joinSplittedWords($paragraph);
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

            if ($this->isPdfLink($element)) {
                continue;
            }

            yield $element;
        }
    }

    public function isPdfLink(Element $element): bool
    {
        if (trim($element->text()) == 'Descargar en formato pdf') {
            return true;
        }

        if (strpos($element->text(), 'La versión HTML de este documento no es oficial') !== false) {
            return true;
        }

        if (strpos($element->text(), 'Formato de archivo en PDF/Adobe Acrobat') !== false) {
            return true;
        }

        if (strpos($element->text(), 'Ver anexo') !== false) {
            return true;
        }

        return false;
    }

    public function joinSplittedWords(string $text): string
    {
        $text = str_replace('A N E x O', 'ANEXO', $text);
        $text = str_replace('B A S E S', 'BASES', $text);
        $text = str_replace('R E S O L V E R', 'RESOLVER', $text);

        $text = str_replace('D I S P 0 N G 0', 'D I S P O N G O', $text);
        $text = str_replace('E x P O N E', 'E X P O N E', $text);
        $text = str_replace('R E s U E L V O', 'R E S U E L V O', $text);
        $text = str_replace('s O L I C I T A', 'S O L I C I T A', $text);

        $text = $this->joinSplittedWord($text, 'ANUNCIO');
        $text = $this->joinSplittedWord($text, 'DISPONGO');
        $text = $this->joinSplittedWord($text, 'EXPONE');
        $text = $this->joinSplittedWord($text, 'RESUELVE');
        $text = $this->joinSplittedWord($text, 'RESUELVO');
        $text = $this->joinSplittedWord($text, 'SOLICITA');
        $text = $this->joinSplittedWord($text, 'TEXTO');

        return $text;
    }

    public function joinSplittedWord(string $text, string $verb): string
    {
        $separated = implode(' ', str_split($verb));

        if (strpos($text, "$separated:") !== false) {
            $text = "$verb:\n\n".trim(str_replace("$separated:", '', $text));
        }

        if (strpos($text, $separated) !== false) {
            $text = "$verb:\n\n".trim(str_replace($separated, '', $text));
        }

        return $text;
    }
}
