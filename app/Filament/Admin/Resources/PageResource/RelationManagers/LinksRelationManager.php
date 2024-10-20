<?php

namespace App\Filament\Admin\Resources\PageResource\RelationManagers;

use App\Http\BocUrl;
use App\Models\Link;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LinksRelationManager extends RelationManager
{
    protected static string $relationship = 'links';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('page.name')
                    ->url(fn (Link $record): string => $record->url)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('url')
                    ->url(fn (Link $record): string => $record->url)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('page.created_at')
                    ->url(fn (Link $record): string => route('filament.admin.resources.pages.view', ['record' => $record->page]))
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ]);
    }
}
