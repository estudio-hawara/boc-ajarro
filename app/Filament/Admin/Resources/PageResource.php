<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Pages;
use App\Http\BocUrl;
use App\Models\Page;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->url(fn (Page $record): string => BocUrl::{$record->name}->value)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(function (string $state) {
                        return strip_tags($state);
                    })
                    ->limit(150),
                Tables\Columns\TextColumn::make('length')
                    ->state(fn (Page $record): string => mb_strlen($record))
                    ->formatStateUsing(fn (string $state): string => number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\Select::make('name')
                            ->options(array_column(BocUrl::cases(), 'name')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name): Builder => $query->whereName($name),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
        ];
    }
}
