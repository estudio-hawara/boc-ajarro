<?php

use App\Jobs\Boc\DownloadRobots;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('stores the downloaded robots.txt locally', function () {
    // Prepare
    Http::fake(fn () => Http::response('User-agent: *', 200));
    Storage::fake();

    // Act
    DownloadRobots::dispatch()->handle();

    // Assert
    Storage::disk('local')->assertExists('robots.txt');
});
