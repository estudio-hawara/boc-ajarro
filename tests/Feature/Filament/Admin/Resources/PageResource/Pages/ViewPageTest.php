<?php

use App\Filament\Admin\Resources\PageResource;
use App\Models\Page;
use App\Models\User;

test('can view the details of a downloaded page', function () {
    // Prepare
    $page = Page::factory()->create();

    // Act and assert
    $this->actingAs(User::factory()->create());
    $this->get(PageResource::getUrl('view', ['record' => $page]))
        ->assertSuccessful();
});

test('when content is shared, a link to the related page is shown', function () {
    // Prepare
    $relatedPage = Page::factory()->create();

    $page = Page::create([
        'name' => $relatedPage->name,
        'url' => $relatedPage->url,
        'content' => null,
        'shared_content_with_page_id' => $relatedPage->id,
        'created_at' => \Carbon\Carbon::now(),
    ]);

    $linkToRelatedPage = route(
        'filament.admin.resources.pages.view',
        ['record' => $relatedPage],
    );

    // Act and assert
    $this->actingAs(User::factory()->create());
    $this->get(PageResource::getUrl('view', ['record' => $page]))
        ->assertSuccessful()
        ->assertSee($linkToRelatedPage);
});
