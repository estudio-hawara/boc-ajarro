<?php

use App\Jobs\Boc\DownloadBulletinIndex;
use App\Jobs\Boc\ExtractBulletinIndexLinks;
use App\Jobs\DownloadPage;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadBulletinIndex('1980', '001');

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/1980/001/');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});

test('links are extracted using the proper extractor', function () {
    // Prepare
    Queue::fake();
    $job = new DownloadBulletinIndex('1980', '001');

    // Act
    $job->handle();

    Queue::assertPushed(ExtractBulletinIndexLinks::class);
});
