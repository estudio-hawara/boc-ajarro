<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LinkResource\Pages;
use App\Http\BocUrl;
use App\Models\Link;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('page.name')
                    ->url(fn (Link $record): string => BocUrl::{$record->page->name}->value)
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

                Tables\Filters\Filter::make('created_at')
                    ->form([

                        Forms\Components\DatePicker::make('date_from')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('date_to')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $dateFrom): Builder => $query->where('created_at', '>=', $dateFrom),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $dateTo): Builder => $query->where('created_at', '<=', $dateTo),
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
            'index' => Pages\ListLinks::route('/'),
        ];
    }
}
