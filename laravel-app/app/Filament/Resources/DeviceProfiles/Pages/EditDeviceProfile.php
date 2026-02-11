<?php
namespace App\Filament\Resources\DeviceProfiles\Pages;
use App\Filament\Resources\DeviceProfiles\DeviceProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDeviceProfile extends EditRecord
{
    protected static string $resource = DeviceProfileResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
