<?php

use App\Filament\Admin\Resources\PageResource;
use App\Models\Page;
use App\Models\User;

use function Pest\Livewire\livewire;

test('can list downloaded pages', function() {
    // Prepare
    $pages = Page::factory()->count(10)->create();

    // Act
    $this->actingAs(User::factory()->create());
    $this->get(PageResource::getUrl('index'))->assertSuccessful();

    // Assert
    livewire(PageResource\Pages\ListPages::class)
        ->assertCanSeeTableRecords($pages);
});