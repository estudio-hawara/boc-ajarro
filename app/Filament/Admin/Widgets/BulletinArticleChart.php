<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Snapshot;
use Filament\Widgets\ChartWidget;

class BulletinArticleChart extends ChartWidget
{
    protected static ?string $heading = 'Bulletin articles';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Snapshot::select('total_bulletin_article', 'missing_bulletin_article', 'disallowed_bulletin_article', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Found',
                    'data' => $data->pluck('total_bulletin_article')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'borderColor' => 'blue',
                ],
                [
                    'label' => 'Missing',
                    'data' => $data->pluck('missing_bulletin_article')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'borderColor' => 'orange',
                ],
                [
                    'label' => 'Disallowed',
                    'data' => $data->pluck('disallowed_bulletin_article')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'borderColor' => 'red',
                ],
            ],
            'labels' => $data->pluck('created_at')
                ->reverse()
                ->values()
                ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d/m/Y'))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
