<?php

namespace App\Filament\Resources\CallLogs\Pages;

use App\Filament\Resources\CallLogs\CallLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCallLogs extends ManageRecords
{
    protected static string $resource = CallLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
