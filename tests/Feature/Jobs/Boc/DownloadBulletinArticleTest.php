<?php

use App\Jobs\Boc\DownloadBulletinArticle;
use App\Jobs\DownloadPage;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $job = new DownloadBulletinArticle('1980', '001', '001');

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/1980/001/001.html');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});
