<?php

use App\Http\BocUrl;
use App\Models\Link;
use App\Models\Page;

test('the content of the related page can be accessed', function () {
    // Prepare
    $relatedContent = 'related-content';

    $page = Page::create([
        'name' => 'page',
        'url' => 'http://localhost',
        'content' => $relatedContent,
    ]);

    // Act
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'http://localhost#link',
    ]);

    // Assert
    expect($link->getContent())->toBe($relatedContent);
});

test('links can be scoped to the ones found in a page', function () {
    // Prepare
    $archive = Page::create([
        'name' => BocUrl::Archive->name,
        'url' => 'http://localhost/archivo/',
    ]);

    $yearIndex = Page::create([
        'name' => BocUrl::YearIndex->name,
        'url' => 'http://localhost/archivo/1980/',
    ]);

    Link::create([
        'page_id' => $archive->id,
        'type' => BocUrl::YearIndex->name,
        'url' => 'http://localhost/archivo/1980/',
    ]);

    Link::create([
        'page_id' => $yearIndex->id,
        'type' => BocUrl::BulletinIndex->name,
        'url' => 'http://localhost/archivo/1980/001/',
    ]);

    // Act
    $count = Link::foundIn(BocUrl::Archive)->count();

    // Assert
    expect($count)->toBe(1);
});

test('links can be scoped to the ones where the download started', function () {
    // Prepare
    $page = Page::factory()->create();

    Link::create([
        'page_id' => $page->id,
        'url' => BocUrl::Archive->value,
        'download_started_at' => \Carbon\Carbon::now(),
    ]);

    Link::create([
        'page_id' => $page->id,
        'url' => BocUrl::Robots->value,
        'download_started_at' => null,
    ]);

    // Act
    $downloadStartedCount = Link::downloadStarted()->count();
    $notDownloadStartedCount = Link::notDownloadStarted()->count();

    // Assert
    expect($downloadStartedCount)->toBe(1);
    expect($notDownloadStartedCount)->toBe(1);
});
