<?php

use App\Jobs\DownloadPage;
use App\Models\Page;
use Illuminate\Support\Facades\Http;

test('sum', function () {
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
