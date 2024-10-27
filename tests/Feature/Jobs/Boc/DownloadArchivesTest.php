<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadArchive;
use App\Jobs\Boc\ExtractLinksFromArchive;
use App\Jobs\DownloadPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadArchive;

    // Assert
    expect($job->getUrl())->toBe(BocUrl::Archive->value);
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});

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
