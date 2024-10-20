<?php

use App\Http\BocUrl;
use App\Jobs\Boc\ExtractArchiveLinks;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
use Mockery\MockInterface;

test('only the year links are extracted', function() {
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

    $job = ExtractArchiveLinks::dispatch($page->id);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->url)->toBe('https://www.gobiernodecanarias.org/boc/archivo/1980/');
});

test('links are not added twice', function() {
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

    $job = ExtractArchiveLinks::dispatch($page->id);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('extract page link jobs are used behind the hood', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['name'] = BocUrl::Archive->name;
    $page->save();

    // Act
    ExtractArchiveLinks::dispatch($page->id);

    // Assert
    expect(is_a(new ExtractArchiveLinks($page->id), ExtractPageLinks::class))->toBeTrue();
});

test('fails with error if the page does not exist', function() {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractArchiveLinks::class, function (MockInterface $mock) {
        $mock->shouldReceive('fail')->once();
    });

    $mock->__construct(pageId: 1);
});

test('fails with error if the page is not an archive page', function() {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractArchiveLinks::class, function (MockInterface $mock) {
        $mock->shouldReceive('fail')->once();
    });

    $page = Page::factory()->make();
    $page->name = 'Landing';
    $page->save();

    $mock->__construct(pageId: $page->id);
});