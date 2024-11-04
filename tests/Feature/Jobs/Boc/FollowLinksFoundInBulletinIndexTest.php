<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinArticle;
use App\Jobs\Boc\FollowLinksFoundInBulletinIndex;
use App\Models\Link;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('takes the next batch of links to process', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    Link::factory()
        ->ofType(BocUrl::BulletinArticle)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html']);

    Link::factory()
        ->ofType(BocUrl::BulletinArticle)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/002.html']);

    Link::factory()
        ->ofType(BocUrl::BulletinArticle)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/003.html']);

    // Act
    FollowLinksFoundInBulletinIndex::dispatch()->handle();

    // Assert
    Queue::assertPushed(DownloadBulletinArticle::class, 3);
});

test('doesn\'t dispatch any job if there are no bulletin article links waiting for download', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    // Act
    FollowLinksFoundInBulletinIndex::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadBulletinArticle::class);
});

test('doesn\'t follow disallowed links', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));
    Storage::fake();

    Link::factory()
        ->ofType(BocUrl::BulletinArticle)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html']);

    Storage::disk('local')
        ->put('robots.txt', "User-agent: *\nDisallow: /boc/archivo/1980/001/001.html");

    // Act
    FollowLinksFoundInBulletinIndex::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadBulletinArticle::class);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    // Act
    FollowLinksFoundInBulletinIndex::dispatch();
    FollowLinksFoundInBulletinIndex::dispatch();
    FollowLinksFoundInBulletinIndex::dispatch();

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
