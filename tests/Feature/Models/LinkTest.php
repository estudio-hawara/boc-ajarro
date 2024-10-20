<?php

use App\Models\Link;
use App\Models\Page;

test("the content of the related page can be accessed", function() {
    // Prepare
    $relatedContent = 'related-content';

    $page = Page::create([
        'name' => 'page',
        'url' => 'http://localhost',
        'content' => $relatedContent,
    ]);

    // Act
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'http://localhost#link',
    ]);

    // Assert
    expect($link->getContent())->toBe($relatedContent);
});