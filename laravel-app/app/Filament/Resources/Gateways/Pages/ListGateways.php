<?php
namespace App\Filament\Resources\Gateways\Pages;
use App\Filament\Resources\Gateways\GatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListGateways extends ListRecords
{
    protected static string $resource = GatewayResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
