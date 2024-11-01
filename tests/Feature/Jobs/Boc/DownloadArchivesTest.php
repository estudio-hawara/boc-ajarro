<?php

use App\Jobs\Boc\DownloadArchive;
use App\Jobs\Boc\ExtractLinksFromArchive;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('links are extracted using the proper extractor', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    $job = new DownloadArchive;

    // Act
    $job->handle();

    // Assert
    Queue::assertPushed(ExtractLinksFromArchive::class);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    // Act
    DownloadArchive::dispatch();
    DownloadArchive::dispatch();
    DownloadArchive::dispatch();

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
