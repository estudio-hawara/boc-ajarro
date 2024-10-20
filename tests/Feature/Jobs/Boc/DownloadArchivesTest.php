<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadArchive;
use App\Jobs\DownloadPage;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadArchive;

    // Assert
    expect($job->getUrl())->toBe(BocUrl::Archive->value);
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});
