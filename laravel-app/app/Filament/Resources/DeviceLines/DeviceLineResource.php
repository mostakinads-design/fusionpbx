<?php

namespace App\Filament\Resources\DeviceLines;

use App\Filament\Resources\DeviceLines\Pages;
use App\Models\DeviceLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceLineResource extends Resource
{
    protected static ?string $model = DeviceLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    
    protected static ?string $navigationGroup = 'Devices';
    
    protected static ?string $navigationLabel = 'Device Lines';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('device_uuid')
                            ->relationship('device', 'device_label')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Device'),
                        
                        Forms\Components\TextInput::make('line_number')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->label('Line Number'),
                        
                        Forms\Components\TextInput::make('server_address')
                            ->maxLength(255)
                            ->label('Server Address'),
                        
                        Forms\Components\TextInput::make('outbound_proxy')
                            ->maxLength(255)
                            ->label('Outbound Proxy'),
                        
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('shared_line')
                            ->label('Shared Line')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.device_label')
                    ->label('Device')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('line_number')
                    ->label('Line')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('server_address')
                    ->label('Server')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('shared_line')
                    ->label('Shared')
                    ->boolean()
                    ->trueIcon('heroicon-o-user-group')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->boolean()
                    ->native(false),
                
                Tables\Filters\TernaryFilter::make('shared_line')
                    ->label('Shared')
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceLines::route('/'),
            'create' => Pages\CreateDeviceLine::route('/create'),
            'edit' => Pages\EditDeviceLine::route('/{record}/edit'),
        ];
    }
}
