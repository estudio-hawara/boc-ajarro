<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Snapshot;
use Filament\Widgets\ChartWidget;

// @codeCoverageIgnoreStart

class BulletinIndexChart extends ChartWidget
{
    protected static ?string $heading = 'Bulletin indexes';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Snapshot::select('total_bulletin_index', 'missing_bulletin_index', 'disallowed_bulletin_index', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Links to bulletin indexes',
                    'data' => $data->pluck('total_bulletin_index')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#2494B3',
                    'borderColor' => '#2494B3',
                ],
                [
                    'label' => 'Missing bulletin indexes',
                    'data' => $data->pluck('missing_bulletin_index')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#E0B841',
                    'borderColor' => '#E0B841',
                ],
                [
                    'label' => 'Disallowed bulletin indexes',
                    'data' => $data->pluck('disallowed_bulletin_index')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#D33F49',
                    'borderColor' => '#D33F49',
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
        return 'bar';
    }
}

// @codeCoverageIgnoreEnd
