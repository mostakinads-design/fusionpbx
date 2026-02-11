<?php

namespace App\Filament\Resources\DeviceLogs;

use App\Filament\Resources\DeviceLogs\Pages;
use App\Models\DeviceLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceLogResource extends Resource
{
    protected static ?string $model = DeviceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'Monitoring';
    
    protected static ?string $navigationLabel = 'Device Logs';
    
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false; // Logs are system generated
    }

    public static function canEdit($record): bool
    {
        return false; // Logs are immutable
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('insert_date')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('device.device_label')
                    ->label('Device')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('remote_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('http_response_code')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 400 => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('http_method')
                    ->label('Method')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('http_uri')
                    ->label('URI')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('http_user_agent')
                    ->label('User Agent')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('http_response_code')
                    ->label('Status Code')
                    ->options([
                        '200' => '200 OK',
                        '404' => '404 Not Found',
                        '500' => '500 Server Error',
                    ]),
                
                Tables\Filters\SelectFilter::make('http_method')
                    ->label('Method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from'),
                        Forms\Components\DatePicker::make('date_to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('insert_date', '>=', $date))
                            ->when($data['date_to'], fn ($q, $date) => $q->whereDate('insert_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('insert_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceLogs::route('/'),
            'view' => Pages\ViewDeviceLog::route('/{record}'),
        ];
    }
}
