<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadBulletinArticle;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Support\Facades\Http;

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

test('throws an exception for links of the wrong type', function () {
    // Prepare
    $page = new Page;
    $page->name = BocUrl::Archive->name;

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/archivo/1980/';
    $link->page = $page;

    // Act
    new DownloadBulletinArticle($link);

    // Assert
})->throws(\InvalidArgumentException::class);
