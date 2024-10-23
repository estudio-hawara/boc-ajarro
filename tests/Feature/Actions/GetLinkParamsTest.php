<?php

use App\Actions\GetLinkParams;
use App\Http\BocUrl;
use App\Models\Link;
use App\Models\Page;

test('can extract the params of a bulletin article link', function () {
    // Prepare
    $page = Page::factory()->create(['name' => BocUrl::BulletinIndex->name]);
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/1991/012/123.html'
    ]);

    // Act
    $params = new GetLinkParams($link);

    // Assert
    expect($params->year)->toBe('1991');
    expect($params->bulletin)->toBe('012');
    expect($params->article)->toBe('123');
});

test('can extract the params of a bulletin index link', function () {
    // Prepare
    $page = Page::factory()->create(['name' => BocUrl::YearIndex->name]);
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/1990/321/'
    ]);

    // Act
    $params = new GetLinkParams($link);

    // Assert
    expect($params->year)->toBe('1990');
    expect($params->bulletin)->toBe('321');
    expect($params->article)->toBeNull();
});

test('can extract the params of a year index link', function () {
    // Prepare
    $page = Page::factory()->create(['name' => BocUrl::Archive->name]);
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'https://www.gobiernodecanarias.org/boc/1985/'
    ]);

    // Act
    $params = new GetLinkParams($link);

    // Assert
    expect($params->year)->toBe('1985');
    expect($params->bulletin)->toBeNull();
    expect($params->article)->toBeNull();
});

test('returns null params for other links', function () {
    // Prepare
    $page = Page::factory()->create(['name' => BocUrl::BulletinIndex->name]);
    $link = Link::create([
        'page_id' => $page->id,
        'url' => 'https://www.gobiernodecanarias.org/robots.txt'
    ]);

    // Act
    $params = new GetLinkParams($link);

    // Assert
    expect($params->year)->toBeNull();
    expect($params->bulletin)->toBeNull();
    expect($params->article)->toBeNull();
});