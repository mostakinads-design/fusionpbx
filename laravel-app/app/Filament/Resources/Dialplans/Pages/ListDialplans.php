<?php
namespace App\Filament\Resources\Dialplans\Pages;
use App\Filament\Resources\Dialplans\DialplanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDialplans extends ListRecords
{
    protected static string $resource = DialplanResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
