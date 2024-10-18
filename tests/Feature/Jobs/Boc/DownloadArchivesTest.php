<?php

use App\Http\BocUrl;
use App\Jobs\Boc\DownloadArchive;
use Illuminate\Support\Facades\Queue;

test('download page jobs are used behind the hood', function () {
    // Prepare
    Queue::fake();

    // Act
    DownloadArchive::dispatch();

    // Assert
    Queue::assertPushed(DownloadArchive::class);
    expect((new DownloadArchive)->getUrl())->toBe(BocUrl::Archive->value);
});
