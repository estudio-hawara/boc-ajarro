<?php

use App\Models\Page;

test("content can be accessed even if it's via relationship", function () {
    // Prepare
    $sharedContent = 'shared-content';

    $firstPage = Page::create([
        'name' => 'page',
        'url' => 'http://localhost',
        'content' => $sharedContent,
    ]);

    // Act
    $secondPage = Page::create([
        'name' => 'page',
        'url' => 'http://localhost',
        'shared_content_with_page_id' => $firstPage['id'],
    ]);

    // Assert
    expect($secondPage->getContent())->toBe($sharedContent);
});
