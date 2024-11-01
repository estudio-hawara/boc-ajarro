<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadYearIndex;
use App\Jobs\Boc\ExtractLinksFromYearIndex;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('links are extracted using the proper extractor', function () {
    // Prepare
    Http::fake(fn () => Http::response('', 200));
    Queue::fake();
    $page = new Page;
    $page->name = BocUrl::Archive->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    $job = new DownloadYearIndex($link);

    // Act
    $job->handle();

    Queue::assertPushed(ExtractLinksFromYearIndex::class);
});

test('download started dates are reset in case of failure', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::Archive->name;
    $page->url = 'https://www.gobiernodecanarias.org/boc/archivo/';
    $page->save();

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page_id = $page->id;
    $link->type = BocUrl::YearIndex->name;
    $link->download_started_at = \Carbon\Carbon::now();
    $link->save();

    Http::fake(fn () => Http::response('', 404));

    // Act
    DownloadYearIndex::dispatch($link)->handle();
    $link->refresh();

    // Assert
    expect($link->download_started_at)->toBeNull();
});

test('throws an exception for links of the wrong type', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::BulletinArticle->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html';
    $link->page = $page;

    // Act
    new DownloadYearIndex($link);

    // Assert
})->throws(\InvalidArgumentException::class);

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    $link = Link::factory()
        ->ofType(BocUrl::YearIndex)
        ->create();

    // Act
    DownloadYearIndex::dispatch($link);
    DownloadYearIndex::dispatch($link);
    DownloadYearIndex::dispatch($link);

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
