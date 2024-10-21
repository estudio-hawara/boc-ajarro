<?php

use App\Jobs\Boc\DownloadBulletinArchive;
use App\Jobs\Boc\ExtractBulletinArchiveLinks;
use App\Jobs\DownloadPage;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadBulletinArchive('1980', '001', '001');

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/1980/001/001.html');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});
