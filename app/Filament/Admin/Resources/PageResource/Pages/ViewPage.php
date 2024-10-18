<?php

namespace App\Filament\Admin\Resources\PageResource\Pages;

use App\Filament\Admin\Resources\PageResource;
use App\Http\BocUrl;
use App\Models\Page;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([

                Infolists\Components\TextEntry::make('name')
                    ->url(fn (Page $record): string => BocUrl::{$record->name}->value),

                Infolists\Components\TextEntry::make('shared_content_with_page_id')
                    ->url(function (Page $record): string {
                        if (! $record?->pageWithSharedContent) {
                            return '';
                        }

                        return route(
                            'filament.admin.resources.pages.view',
                            ['record' => $record?->pageWithSharedContent]
                        );
                    }),

                Infolists\Components\TextEntry::make('created_at')
                    ->datetime(),

                Infolists\Components\TextEntry::make('content')
                    ->columnSpanFull()
                    ->hidden(fn (Page $record): bool => ! $record->content)
                    ->helperText('Click to copy')
                    ->lineClamp(10)
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Copied!'),

            ])->columns(3);
    }
}
