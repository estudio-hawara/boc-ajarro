<?php

use App\Http\BocUrl;
use App\Jobs\TakeSnapshot;
use App\Models\Link;
use App\Models\Snapshot;

test('count the number of links and pages of each type', function () {
    // Prepare
    Link::factory(1)
        ->ofType(BocUrl::YearIndex)
        ->create();

    Link::factory(2)
        ->ofType(BocUrl::BulletinIndex)
        ->create();

    Link::factory(3)
        ->ofType(BocUrl::BulletinArticle)
        ->create();

    // Act
    TakeSnapshot::dispatch()->handle();
    $snapshot = Snapshot::first();

    // Assert
    expect($snapshot->total_year_index)->toBe(1);
    expect($snapshot->total_bulletin_index)->toBe(2);
    expect($snapshot->total_bulletin_article)->toBe(3);
    expect($snapshot->missing_year_index)->toBe(1);
    expect($snapshot->missing_bulletin_index)->toBe(2);
    expect($snapshot->missing_bulletin_article)->toBe(3);
});