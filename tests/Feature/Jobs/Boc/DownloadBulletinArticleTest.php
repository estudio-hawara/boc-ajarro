<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinArticle;
use App\Jobs\DownloadPage;
use App\Models\Link;
use App\Models\Page;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $page = new Page();
    $page->name = BocUrl::BulletinIndex->name;

    $link = new Link();
    $link->url = 'https://www.gobiernodecanarias.org/boc/1980/001/001.html';
    $link->page = $page;

    $job = new DownloadBulletinArticle($link);

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/1980/001/001.html');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});
