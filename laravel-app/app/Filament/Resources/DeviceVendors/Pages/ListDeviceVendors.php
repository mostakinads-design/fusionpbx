<?php
namespace App\Filament\Resources\DeviceVendors\Pages;
use App\Filament\Resources\DeviceVendors\DeviceVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDeviceVendors extends ListRecords
{
    protected static string $resource = DeviceVendorResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
