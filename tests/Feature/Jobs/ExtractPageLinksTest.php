<?php

use App\Http\BocUrl;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
use Mockery\MockInterface;

test('links of a page are extracted', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['content'] = '
        <html>
        <body>
            <a href="#first">First</a>
            <a href="#second">Second</a>
        </body>
        </html>';
    $page->save();

    $job = ExtractPageLinks::dispatch($page, 'http://localhost');

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('links of a page can be recreated if they existed already', function () {
    // Prepare
    $page = Page::factory()->make();
    $page['content'] = '
        <html>
        <body>
            <a href="#first">First</a>
        </body>
        </html>';
    $page->save();

    $link = $page->links()->create([
        'url' => 'http://localhost#first',
    ]);

    $job = ExtractPageLinks::dispatch($page, 'http://localhost', true);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->id >= $link->id)->toBeTrue();
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

    // Act
    ExtractPageLinks::dispatch($page, 'http://localhost')->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('aborts the extraction if the dom could not be parsed', function () {
    // Prepare
    $page = Page::factory()->make();

    // Act and assert
    $mock = $this->partialMock(ExtractPageLinks::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')->once();
    });

    $mock->__construct($page, 'http://localhost');
    $mock->handle();
});
