<?php

use App\Http\BocUrl;

test('url enumeration cases can be found from their names', function () {
    // Prepare
    $case = 'Archive';

    // Act
    $found = BocUrl::fromName($case);

    // Assert
    expect($found)->toBe(BocUrl::Archive);
});

test('search by a non existing name returns null', function () {
    // Prepare
    $case = 'Invalid';

    // Act
    $found = BocUrl::fromName($case);

    // Assert
    expect($found)->toBeNull();
});

test('can tell where a link type is found', function () {
    // Prepare
    $yearIndex = BocUrl::YearIndex;

    // Act
    $foundIn = $yearIndex->foundIn();

    // Assert
    expect($foundIn)->toBe(BocUrl::Archive);
});
