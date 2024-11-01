<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinArticle;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

test('download started dates are reset in case of failure', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::BulletinIndex->name;
    $page->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/';
    $page->save();

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html';
    $link->page_id = $page->id;
    $link->type = BocUrl::BulletinArticle->name;
    $link->download_started_at = \Carbon\Carbon::now();
    $link->save();

    Http::fake(fn () => Http::response('', 404));

    // Act
    DownloadBulletinArticle::dispatch($link)->handle();
    $link->refresh();

    // Assert
    expect($link->download_started_at)->toBeNull();
});

test('does not get executed if the link is of the wrong type', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::Archive->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    // Act and assert
    $mock = $this->partialMock(DownloadBulletinArticle::class, function (MockInterface $mock) {
        $mock->shouldReceive('logAndDelete')->once();
    });

    $mock->__construct($link);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    $link = Link::factory()
        ->ofType(BocUrl::BulletinArticle)
        ->create();

    // Act
    DownloadBulletinArticle::dispatch($link);
    DownloadBulletinArticle::dispatch($link);
    DownloadBulletinArticle::dispatch($link);

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
