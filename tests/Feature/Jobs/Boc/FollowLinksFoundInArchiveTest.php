<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadYearIndex;
use App\Jobs\Boc\FollowLinksFoundInArchive;
use App\Models\Link;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('takes the next batch of links to process', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    Link::factory()
        ->ofType(BocUrl::YearIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/']);

    Link::factory()
        ->ofType(BocUrl::YearIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1981/']);

    Link::factory()
        ->ofType(BocUrl::YearIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1982/']);     

    // Act
    FollowLinksFoundInArchive::dispatch(limit: 2)->handle();

    // Assert
    Queue::assertPushed(DownloadYearIndex::class, 2);
});

test('doesn\'t dispatch any job if there are no year index links waiting for download', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));

    // Act
    FollowLinksFoundInArchive::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadYearIndex::class);
});

test('doesn\'t follow disallowed links', function () {
    // Prepare
    Queue::fake();
    Http::fake(fn () => Http::response('', 200));
    Storage::fake();

    Link::factory()
        ->ofType(BocUrl::YearIndex)
        ->create(['url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/']);

    Storage::disk('local')
        ->put('robots.txt', "User-agent: *\nDisallow: /boc/archivo/1980/");

    // Act
    FollowLinksFoundInArchive::dispatch()->handle();

    // Assert
    Queue::assertNotPushed(DownloadYearIndex::class);
});