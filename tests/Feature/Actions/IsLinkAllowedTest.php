<?php

use App\Actions\IsLinkAllowed;
use App\Models\Link;
use Illuminate\Support\Facades\Storage;

test('can tell if a link is disallowed', function () {
    // Prepare
    Storage::fake();

    Storage::disk('local')
        ->put('robots.txt', "User-agent: *\nDisallow: /boc/1988/152/");

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/1988/152/';

    // Act
    $check = new IsLinkAllowed($link);

    // Assert
    expect($check->allowed)->toBe(false);
});

test('does identify allowed links', function () {
    // Prepare
    Storage::fake();

    Storage::disk('local')
        ->put('robots.txt', "User-agent: *\nDisallow: /boc/1988/152/");

    $link = new Link;
    $link->url = 'https://www.gobiernodecanarias.org/boc/1980/001/';

    // Act
    $check = new IsLinkAllowed($link);

    // Assert
    expect($check->allowed)->toBe(true);
});
