<?php

use App\Actions\GetParsedDom;
use App\Models\Page;
use voku\helper\HtmlDomParser;

test('can parse an html page', function () {
    // Prepare
    $page = Page::factory()->create();

    // Act
    $parsing = new GetParsedDom($page->id);

    // Assert
    expect(is_a($parsing->dom, HtmlDomParser::class))->toBeTrue();
    expect($parsing->error)->toBeNull();
});

test('fails if the page does not exist', function () {
    // Prepare and act
    $parsing = new GetParsedDom(1);

    // Assert
    expect(is_string($parsing->error))->toBeTrue();
    expect($parsing->dom)->toBeNull();
});

test('fails if the page content is from a related page download', function () {
    // Prepare
    $firstPage = Page::factory()->create();
    $secondPage = Page::create([
        'name' => $firstPage->name,
        'url' => $firstPage->url,
        'content' => null,
        'shared_content_with_page_id' => $firstPage->id,
    ]);

    // Act
    $parsing = new GetParsedDom($secondPage->id);

    // Assert
    expect(is_string($parsing->error))->toBeTrue();
    expect($parsing->dom)->toBeNull();
});
