<?php

test('empty links are considered auto links', function () {
    // Prepare
    $root = 'https://www.gobiernodecanarias.org';
    $current = 'https://www.gobiernodecanarias.org/boc/archivo/';
    $path = '';

    // Act
    $url = urljoin($root, $current, $path);

    // Assert
    expect($url)->toBe($current);
});

test('relative urls can be solved', function () {
    // Prepare
    $root = 'https://www.gobiernodecanarias.org';
    $current = 'https://www.gobiernodecanarias.org/boc/archivo/';
    $path = './2024';

    // Act
    $url = urljoin($root, $current, $path);

    // Assert
    expect($url)->toBe('https://www.gobiernodecanarias.org/boc/archivo/2024');
});

test('absolute urls can be solved', function () {
    // Prepare
    $root = 'https://www.gobiernodecanarias.org';
    $current = 'https://www.gobiernodecanarias.org/boc/archivo/';
    $path = '/boc/';

    // Act
    $url = urljoin($root, $current, $path);

    // Assert
    expect($url)->toBe('https://www.gobiernodecanarias.org/boc/');
});

test('external urls are understood', function () {
    // Prepare
    $root = 'https://www.gobiernodecanarias.org';
    $current = 'https://www.gobiernodecanarias.org/boc/archivo/';
    $path = 'https://hawara.es';

    // Act
    $url = urljoin($root, $current, $path);

    // Assert
    expect($url)->toBe('https://hawara.es');
});