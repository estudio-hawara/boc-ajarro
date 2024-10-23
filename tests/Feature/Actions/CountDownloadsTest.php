<?php

use App\Actions\CountDownloads;
use App\Http\BocUrl;
use App\Models\Link;
use App\Models\Page;

test('the number of finished and missing downloads can be counted', function () {
    // Prepare
    $archivePage = Page::create([
        'name' => BocUrl::Archive->name,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/',
    ]);

    Link::create([
        'page_id' => $archivePage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/',
    ]);

    Link::create([
        'page_id' => $archivePage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1981/',
    ]);

    $yearIndexPage = Page::create([
        'name' => BocUrl::YearIndex->name,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/',
    ]);

    Link::create([
        'page_id' => $yearIndexPage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/',
    ]);

    Link::create([
        'page_id' => $yearIndexPage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1981/001/',
    ]);

    $bulletinIndexPage = Page::create([
        'name' => BocUrl::BulletinIndex->name,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/',
    ]);

    Link::create([
        'page_id' => $bulletinIndexPage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html',
    ]);

    Link::create([
        'page_id' => $bulletinIndexPage->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1981/001/001.html',
    ]);

    Page::create([
        'name' => BocUrl::BulletinArticle->name,
        'url' => 'https://www.gobiernodecanarias.org/boc/archivo/1980/001/001.html',
    ]);

    // Act
    $count = new CountDownloads;

    // Assert
    expect($count->totalYearIndex)->toBe(2);
    expect($count->totalBulletinIndex)->toBe(2);
    expect($count->totalBulletinArticle)->toBe(2);

    expect($count->missingYearIndex)->toBe(1);
    expect($count->missingBulletinIndex)->toBe(1);
    expect($count->missingBulletinArticle)->toBe(1);
});
