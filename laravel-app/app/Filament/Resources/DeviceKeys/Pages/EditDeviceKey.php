<?php
namespace App\Filament\Resources\DeviceKeys\Pages;
use App\Filament\Resources\DeviceKeys\DeviceKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDeviceKey extends EditRecord
{
    protected static string $resource = DeviceKeyResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
