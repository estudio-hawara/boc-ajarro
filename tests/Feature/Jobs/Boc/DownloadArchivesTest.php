<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadArchives;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare
    Queue::fake();

    // Act
    DownloadArchives::dispatch();

    // Assert
    Queue::assertPushed(DownloadArchives::class);
    expect((new DownloadArchives)->getUrl())->toBe(BocUrl::Archive->value);
});
