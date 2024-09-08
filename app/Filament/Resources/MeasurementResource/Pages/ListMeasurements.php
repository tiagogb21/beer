<?php

namespace App\Filament\Resources\MeasurementResource\Pages;

use App\Filament\Resources\MeasurementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeasurements extends ListRecords
{
    protected static string $resource = MeasurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
