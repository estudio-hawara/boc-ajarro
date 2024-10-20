<?php

use App\Jobs\ExtractPageLinks;
use App\Models\Page;

test('links of a page are extracted', function() {
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

    $job = ExtractPageLinks::dispatch($page->id, 'http://localhost');

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(2);
});

test('links of a page can be recreated if they existed already', function() {
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

    $job = ExtractPageLinks::dispatch($page->id, 'http://localhost', true);

    // Act
    $job->handle();

    // Assert
    $page->refresh();
    expect($page->links->count())->toBe(1);
    expect($page->links->first()->id >= $link->id)->toBeTrue();
});

test('fails with error if the page does not exist', function() {
    // Prepare
    $mock = \Mockery::mock('App\Jobs\ExtractPageLinks[fail]', [1, 'http://localhost']);

    // Act and assert
    $mock->shouldReceive('fail')
        ->once();

    /** @var ExtractPageLinks $mock */
    $mock->handle();
});