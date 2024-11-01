<?php

use App\Http\BocUrl;
use App\Jobs\Boc\ExtractLinksFromBulletinIndex;
use App\Models\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

test('only the bulletin article links are extracted', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::BulletinIndex->name;
    $page['content'] = '
        <html>
        <body>
            <a href="#first">Invalid</a>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/001.html">Valid</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromBulletinIndex::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->url)->toBe('https://www.gobiernodecanarias.org/boc/1980/001/001.html');
});

test('links are not added twice', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page['content'] = '
        <html>
        <body>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/001.html">New</a>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/001.html">Duplicated</a>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/002.html">Also new</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromBulletinIndex::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('is deleted from the queue if the page does not exist', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromBulletinIndex::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = new Page;
    $page->id = -1;

    $mock->__construct($page);
});

test('is deleted from the queue if the page is not a bulletin index', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromBulletinIndex::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = Page::factory()->make();
    $page->name = 'Landing';
    $page->save();

    $mock->__construct($page);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_extractions', 2);
    Queue::fake();

    $page = Page::factory()->make();
    $page['name'] = BocUrl::BulletinIndex->name;
    $page['content'] = '';
    $page->save();

    // Act
    ExtractLinksFromBulletinIndex::dispatch($page);
    ExtractLinksFromBulletinIndex::dispatch($page);
    ExtractLinksFromBulletinIndex::dispatch($page);

    // Assert
    expect(Queue::size('extract'))->toBe(2);
});
