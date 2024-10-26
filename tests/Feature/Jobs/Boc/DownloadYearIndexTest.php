<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadYearIndex;
use App\Jobs\Boc\ExtractBulletinIndexLinksFromYearIndex;
use App\Jobs\DownloadPage;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $page = new Page();
    $page->name = BocUrl::Archive->name;

    $link = new Link();
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    $job = new DownloadYearIndex($link);

    // Assert
    expect($job->getUrl())->toBe('https://www.gobiernodecanarias.org/boc/archivo/1980/');
    expect(is_a($job, DownloadPage::class))->toBeTrue();
});

test('links are extracted using the proper extractor', function () {
    // Prepare
    Queue::fake();
    $page = new Page();
    $page->name = BocUrl::Archive->name;

    $link = new Link();
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    $job = new DownloadYearIndex($link);

    // Act
    $job->handle();

    Queue::assertPushed(ExtractBulletinIndexLinksFromYearIndex::class);
});
