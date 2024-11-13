<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Snapshot;
use Filament\Widgets\ChartWidget;

// @codeCoverageIgnoreStart

class YearIndexChart extends ChartWidget
{
    protected static ?string $heading = 'Year indexes';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Snapshot::select('total_year_index', 'missing_year_index', 'disallowed_year_index', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Links to year indexes',
                    'data' => $data->pluck('total_year_index')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#2494B3',
                    'borderColor' => '#2494B3',
                ],
                [
                    'label' => 'Missing year indexes',
                    'data' => $data->pluck('missing_year_index')
                        ->reverse()
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#E0B841',
                    'borderColor' => '#E0B841',
                ],
                [
                    'label' => 'Disallowed year indexes',
                    'data' => $data->pluck('disallowed_year_index')
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
