<?php

namespace App\Filament\Admin\Resources\PageResource\Pages;

use App\Filament\Admin\Resources\PageResource;
use App\Models\Page;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Infolists\Components\TextEntry::make('name')
                    ->url(fn (Page $record): string => $record->url),

                Infolists\Components\TextEntry::make('length')
                    ->state(fn (Page $record): string => mb_strlen($record->getContent()))
                    ->numeric(),

                Infolists\Components\TextEntry::make('created_at')
                    ->datetime(),

                Infolists\Components\TextEntry::make('shared_content_with_page_id')
                    ->label('Shared content with')
                    ->getStateUsing(function (Page $record) {
                        return $record?->pageWithSharedContent?->created_at;
                    })
                    ->datetime()
                    ->url(function (Page $record): string {
                        if (! $record?->pageWithSharedContent) {
                            return '';
                        }

                        return route(
                            'filament.admin.resources.pages.view',
                            ['record' => $record?->pageWithSharedContent]
                        );
                    }),

                Infolists\Components\TextEntry::make('content')
                    ->getStateUsing(function (Page $record) {
                        $content = mb_convert_encoding($record->getContent(), 'UTF-8', 'UTF-8');
                        return strip_tags($content ?? '');
                    })
                    ->columnSpanFull()
                    ->helperText('Click to copy')
                    ->lineClamp(10)
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Copied!'),

            ])->columns(4);
    }
}
