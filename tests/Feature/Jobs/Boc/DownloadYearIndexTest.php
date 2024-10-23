<?php

use App\Jobs\Boc\DownloadYearIndex;
use App\Jobs\Boc\ExtractYearIndexLinks;
use App\Jobs\DownloadPage;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadYearIndex(1980);

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/archivo/1980/');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});

test('links are extracted using the proper extractor', function () {
    // Prepare
    Queue::fake();
    $job = new DownloadYearIndex(1980);

    // Act
    $job->handle();

    Queue::assertPushed(ExtractYearIndexLinks::class);
});
