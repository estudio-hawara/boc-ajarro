<?php

use App\Http\BocUrl;
use App\Jobs\Boc\ExtractBulletinIndexLinks;
use App\Jobs\ExtractPageLinks;
use App\Models\Page;
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

    $job = ExtractBulletinIndexLinks::dispatch($page->id);

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

    $job = ExtractBulletinIndexLinks::dispatch($page->id);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('extract page link jobs are used behind the hood', function () {
    // Prepare and act
    $page = Page::factory()->make();
    $page['name'] = BocUrl::YearIndex->name;
    $page->save();

    // Assert
    expect(is_a(new ExtractBulletinIndexLinks($page->id), ExtractPageLinks::class))->toBeTrue();
});

test('fails with error if the page does not exist', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractBulletinIndexLinks::class, function (MockInterface $mock) {
        $mock->shouldReceive('fail')->once();
    });

    $mock->__construct(pageId: 1);
});

test('fails with error if the page is not a bulletin index', function () {
    // Prepare, act and assert
    $mock = $this->partialMock(ExtractBulletinIndexLinks::class, function (MockInterface $mock) {
        $mock->shouldReceive('fail')->once();
    });

    $page = Page::factory()->make();
    $page->name = 'Landing';
    $page->save();

    $mock->__construct(pageId: $page->id);
});
