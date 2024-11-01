<?php

use App\Jobs\Boc\DownloadRobots;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
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

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    // Act
    DownloadRobots::dispatch();
    DownloadRobots::dispatch();
    DownloadRobots::dispatch();

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
