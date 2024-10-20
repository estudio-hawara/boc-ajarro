<?php

use App\Filament\Admin\Resources\LinkResource;
use App\Models\Link;
use App\Models\User;

use function Pest\Livewire\livewire;

test('can list extracted links', function () {
    // Prepare
    $links = Link::factory()->count(10)->create();

    // Act
    $this->actingAs(User::factory()->create());
    $this->get(LinkResource::getUrl('index'))->assertSuccessful();

    // Assert
    livewire(LinkResource\Pages\ListLinks::class)
        ->assertCanSeeTableRecords($links);
});
