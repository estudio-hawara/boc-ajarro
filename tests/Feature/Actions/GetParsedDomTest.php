<?php

use App\Actions\GetParsedDom;
use App\Models\Page;
use DiDom\Document;

test('can parse an html page', function () {
    // Prepare
    $page = Page::factory()->create();

    // Act
    $parsing = new GetParsedDom($page);

    // Assert
    expect(is_a($parsing->dom, Document::class))->toBeTrue();
    expect($parsing->error)->toBeNull();
});

test('fails if the page does not exist', function () {
    // Prepare and act
    $page = new Page;
    $page->id = -1;

    $parsing = new GetParsedDom($page);

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
    $parsing = new GetParsedDom($secondPage);

    // Assert
    expect(is_string($parsing->error))->toBeTrue();
    expect($parsing->dom)->toBeNull();
});
