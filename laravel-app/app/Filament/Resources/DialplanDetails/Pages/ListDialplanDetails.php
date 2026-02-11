<?php
namespace App\Filament\Resources\DialplanDetails\Pages;
use App\Filament\Resources\DialplanDetails\DialplanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDialplanDetails extends ListRecords
{
    protected static string $resource = DialplanDetailResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
