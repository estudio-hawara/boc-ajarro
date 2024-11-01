<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinIndex;
use App\Jobs\Boc\FollowLinksFoundInYearIndex;
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
        ->ofType(BocUrl::BulletinIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/']);

    Link::factory()
        ->ofType(BocUrl::BulletinIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/002/']);

    Link::factory()
        ->ofType(BocUrl::BulletinIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/003/']);

    // Act
    FollowLinksFoundInYearIndex::dispatch(limit: 2)->handle();

    // Assert
    Queue::assertPushed(DownloadBulletinIndex::class, 2);
});

test('doesn\'t dispatch any job if there are no bulletin index links waiting for download', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    // Act
    FollowLinksFoundInYearIndex::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadBulletinIndex::class);
});

test('doesn\'t follow disallowed links', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));
    Storage::fake();

    Link::factory()
        ->ofType(BocUrl::BulletinIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/']);

    Storage::disk('local')
        ->put('robots.txt', "User-agent: *\nDisallow: /boc/archivo/1980/001/");

    // Act
    FollowLinksFoundInYearIndex::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadBulletinIndex::class);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_downloads', 2);
    Queue::fake();

    // Act
    FollowLinksFoundInYearIndex::dispatch();
    FollowLinksFoundInYearIndex::dispatch();
    FollowLinksFoundInYearIndex::dispatch();

    // Assert
    expect(Queue::size('download'))->toBe(2);
});
