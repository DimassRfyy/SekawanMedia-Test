<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Vehicle;

class VehicleStats extends BaseWidget
{

    protected function getStats(): array
    {
        $totalVehicles = Vehicle::count();
        $totalAngkutanBarang = Vehicle::where('type', 'angkutan_barang')->count();
        $totalAngkutanOrang = Vehicle::where('type', 'angkutan_orang')->count();
        $totalMilikPerusahaan = Vehicle::where('ownership', 'milik_perusahaan')->count();
        $totalSewaPerusahaan = Vehicle::where('ownership', 'sewa_perusahaan')->count();

        return [
            Stat::make('Total Vehicle', $totalVehicles)
                ->description('Jumlah seluruh kendaraan'),
            Stat::make('Total Angkutan Barang', $totalAngkutanBarang)
                ->description('Kendaraan untuk angkutan barang'),
            Stat::make('Total Angkutan Orang', $totalAngkutanOrang)
                ->description('Kendaraan untuk angkutan orang'),
            Stat::make('Total Milik Perusahaan', $totalMilikPerusahaan)
                ->description('Kendaraan milik perusahaan'),
            Stat::make('Total Sewa Perusahaan', $totalSewaPerusahaan)
                ->description('Kendaraan sewa perusahaan'),
        ];
    }
}
