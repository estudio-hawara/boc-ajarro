<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinIndex;
use App\Jobs\Boc\ExtractLinksFromBulletinIndex;
use App\Jobs\DownloadPage;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare and act
    $page = new Page;
    $page->name = BocUrl::YearIndex->name;

    $link = new Link;
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
    $page = new Page;
    $page->name = BocUrl::YearIndex->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/1980/001/';
    $link->page = $page;

    $job = new DownloadBulletinIndex($link);

    // Act
    $job->handle();

    Queue::assertPushed(ExtractLinksFromBulletinIndex::class);
});

test('download started dates are reset in case of failure', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::YearIndex->name;
    $page->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $page->save();

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/';
    $link->page_id = $page->id;
    $link->type = BocUrl::BulletinIndex->name;
    $link->download_started_at = \Carbon\Carbon::now();
    $link->save();

    Http::fake(fn () => Http::response('', 404));

    // Act
    DownloadBulletinIndex::dispatch($link)->handle();
    $link->refresh();

    // Assert
    expect($link->download_started_at)->toBeNull();
});

test('throws an exception for links of the wrong type', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::Archive->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    // Act
    new DownloadBulletinIndex($link);

    // Assert
})->throws(\InvalidArgumentException::class);
