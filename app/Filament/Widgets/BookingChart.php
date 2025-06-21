<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingChart extends ChartWidget
{
    protected static ?string $heading = 'Total Booking per Bulan';

    protected function getData(): array
    {
        // Ambil total booking per bulan
        $bookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->all();

        // Siapkan label bulan (1-12)
        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('F', mktime(0, 0, 0, $i, 10));
            $data[] = $bookings[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Booking',
                    'data' => $data,
                    'backgroundColor' => '#f59e42',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
