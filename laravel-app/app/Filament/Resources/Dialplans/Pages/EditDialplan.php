<?php
namespace App\Filament\Resources\Dialplans\Pages;
use App\Filament\Resources\Dialplans\DialplanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDialplan extends EditRecord
{
    protected static string $resource = DialplanResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
