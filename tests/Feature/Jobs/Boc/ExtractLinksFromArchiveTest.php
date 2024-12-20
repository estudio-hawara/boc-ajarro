<?php

use App\Http\BocUrl;
use App\Jobs\Boc\ExtractLinksFromArchive;
use App\Models\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

test('only the year links are extracted', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page['content'] = '
        <html>
        <body>
            <a href="#first">Invalid</a>
            <a href="https://www.gobiernodecanarias.org/boc/archivo/1980/">Valid</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromArchive::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->url)->toBe('https://www.gobiernodecanarias.org/boc/archivo/1980/');
});

test('links are not added twice', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page['content'] = '
        <html>
        <body>
            <a href="https://www.gobiernodecanarias.org/boc/archivo/1980/">1980</a>
            <a href="https://www.gobiernodecanarias.org/boc/archivo/1980/">1980</a>
            <a href="https://www.gobiernodecanarias.org/boc/archivo/1981/">1981</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromArchive::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('is deleted from the queue if the page does not exist', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromArchive::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = new Page;
    $page->id = -1;

    $mock->__construct($page);
});

test('is deleted from the queue if the page is not an archive page', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromArchive::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = Page::factory()->make();
    $page->name = 'Landing';
    $page->save();

    $mock->__construct(page: $page);
});

test('is not executed if the queue is already at its maximum', function () {
    // Prepare
    Config::set('app.max_extractions', 2);
    Queue::fake();

    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page['content'] = '';
    $page->save();

    // Act
    ExtractLinksFromArchive::dispatch($page);
    ExtractLinksFromArchive::dispatch($page);
    ExtractLinksFromArchive::dispatch($page);

    // Assert
    expect(Queue::size('extract'))->toBe(2);
});
