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

                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('name')
                    ->url(fn (Page $record): string => BocUrl::{$record->name}->value)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('content')
                    ->getStateUsing(function (Page $record) {
                        return strip_tags($record->getContent());
                    })
                    ->limit(100),

                Tables\Columns\TextColumn::make('length')
                    ->state(fn (Page $record): string => mb_strlen($record->getContent()))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('shared_content_with_page_id')
                    ->label('Shared content with')
                    ->getStateUsing(function (Page $record) {
                        return strip_tags($record?->pageWithSharedContent?->created_at ?? '');
                    })
                    ->datetime(),

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
            'index' => Pages\ListPages::route('/'),
            'view' => Pages\ViewPage::route('/{record}'),
        ];
    }
}
