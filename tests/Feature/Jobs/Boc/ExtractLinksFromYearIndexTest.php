<?php

use App\Http\BocUrl;
use App\Jobs\Boc\ExtractLinksFromYearIndex;
use App\Models\Page;
use Mockery\MockInterface;

test('only the bulletin links are extracted', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::YearIndex->name;
    $page['content'] = '
        <html>
        <body>
            <a href="#first">Invalid</a>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/">Valid</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromYearIndex::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->url)->toBe('https://www.gobiernodecanarias.org/boc/1980/001/');
});

test('links are not added twice', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page['content'] = '
        <html>
        <body>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/">1980</a>
            <a href="https://www.gobiernodecanarias.org/boc/1980/001/">1980</a>
            <a href="https://www.gobiernodecanarias.org/boc/1981/001/">1981</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractLinksFromYearIndex::dispatch($page);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('is deleted from the queue if the page does not exist', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromYearIndex::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = new Page;
    $page->id = -1;

    $mock->__construct($page);
});

test('is deleted from the queue if the page is not a year index', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractLinksFromYearIndex::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $page = Page::factory()->make();
    $page->name = 'Landing';
    $page->save();

    $mock->__construct($page);
});
