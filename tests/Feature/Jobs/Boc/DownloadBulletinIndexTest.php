<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinIndex;
use App\Jobs\Boc\ExtractBulletinArticleLinksFromBulletinIndex;
use App\Jobs\DownloadPage;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $page = new Page();
    $page->name = BocUrl::YearIndex->name;

    $link = new Link();
    $link->url = 'https://www.gobiernodecanarias.org/boc/1980/001/';
    $link->page = $page;

    $job = new DownloadBulletinIndex($link);

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/1980/001/');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});

test('links are extracted using the proper extractor', function () {
    // Prepare
    Queue::fake();
    $page = new Page();
    $page->name = BocUrl::YearIndex->name;

    $link = new Link();
    $link->url = 'https://www.gobiernodecanarias.org/boc/1980/001/';
    $link->page = $page;

    $job = new DownloadBulletinIndex($link);

    // Act
    $job->handle();

    Queue::assertPushed(ExtractBulletinArticleLinksFromBulletinIndex::class);
});
