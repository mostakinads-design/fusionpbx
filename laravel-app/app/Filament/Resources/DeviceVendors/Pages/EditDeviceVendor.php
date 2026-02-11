<?php
namespace App\Filament\Resources\DeviceVendors\Pages;
use App\Filament\Resources\DeviceVendors\DeviceVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDeviceVendor extends EditRecord
{
    protected static string $resource = DeviceVendorResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
