<?php
namespace App\Filament\Resources\DeviceLines\Pages;
use App\Filament\Resources\DeviceLines\DeviceLineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDeviceLines extends ListRecords
{
    protected static string $resource = DeviceLineResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
