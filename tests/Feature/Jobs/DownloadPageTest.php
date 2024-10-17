<?php

use App\Jobs\DownloadPage;
use App\Models\Page;
use Illuminate\Support\Facades\Http;

test('single pages can be downloaded', function () {
    // Prepare
    $url = 'http://localhost';
    $content = '<html></html>';
    Http::fake(fn() => Http::response($content, 200));

    // Act
    (new DownloadPage($url))->handle();

    // Assert
    $page = Page::whereUrl($url)
        ->get()
        ->first();

    expect($page->content)->toBe($content);
});

test('errors during download are managed', function () {
    // Prepare
    $url = 'http://localhost';
    $content = '<html></html>';
    Http::fake(fn() => Http::response($content, 500));

    // Act
    (new DownloadPage($url))->handle();

    // Assert
    $pageExists = Page::whereUrl($url)
        ->get()
        ->count() > 0;

    expect($pageExists)->toBe(false);
});
