<?php
namespace App\Filament\Resources\DeviceKeys\Pages;
use App\Filament\Resources\DeviceKeys\DeviceKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDeviceKeys extends ListRecords
{
    protected static string $resource = DeviceKeyResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
