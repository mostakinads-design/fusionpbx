<?php
namespace App\Filament\Resources\SipProfiles\Pages;
use App\Filament\Resources\SipProfiles\SipProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListSipProfiles extends ListRecords
{
    protected static string $resource = SipProfileResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
