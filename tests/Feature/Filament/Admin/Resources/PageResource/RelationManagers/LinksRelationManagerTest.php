<?php

use App\Filament\Admin\Resources\PageResource;
use App\Models\Link;
use App\Models\Page;

use function Pest\Livewire\livewire;

test('links found in a page are shown', function () {
    $page = Page::factory()
        ->has(Link::factory()->count(5))
        ->create();

    livewire(PageResource\RelationManagers\LinksRelationManager::class, [
        'pageClass' => Page::class,
        'ownerRecord' => $page,
    ])->assertSuccessful();
});
