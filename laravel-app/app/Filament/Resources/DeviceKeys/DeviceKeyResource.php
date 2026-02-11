<?php

namespace App\Filament\Resources\DeviceKeys;

use App\Filament\Resources\DeviceKeys\Pages;
use App\Models\DeviceKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceKeyResource extends Resource
{
    protected static ?string $model = DeviceKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    
    protected static ?string $navigationGroup = 'Devices';
    
    protected static ?string $navigationLabel = 'Function Keys';
    
    protected static ?int $navigationSort = 2;

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
                        
                        Forms\Components\TextInput::make('device_key_id')
                            ->required()
                            ->numeric()
                            ->label('Key ID'),
                        
                        Forms\Components\Select::make('device_key_category')
                            ->options([
                                'line' => 'Line',
                                'blf' => 'BLF',
                                'speed_dial' => 'Speed Dial',
                                'park' => 'Park',
                                'pickup' => 'Pickup',
                            ])
                            ->required()
                            ->label('Category'),
                        
                        Forms\Components\Select::make('device_key_type')
                            ->options([
                                'line' => 'Line',
                                'blf' => 'BLF',
                                'speed_dial' => 'Speed Dial',
                                'dnd' => 'DND',
                                'transfer' => 'Transfer',
                                'conference' => 'Conference',
                            ])
                            ->required()
                            ->label('Type'),
                        
                        Forms\Components\TextInput::make('device_key_line')
                            ->numeric()
                            ->label('Line Number'),
                        
                        Forms\Components\TextInput::make('device_key_value')
                            ->maxLength(255)
                            ->label('Value'),
                        
                        Forms\Components\TextInput::make('device_key_label')
                            ->maxLength(255)
                            ->label('Label'),
                        
                        Forms\Components\Toggle::make('device_key_protected')
                            ->label('Protected')
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
                
                Tables\Columns\TextColumn::make('device_key_id')
                    ->label('Key #')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('device_key_category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'line' => 'info',
                        'blf' => 'success',
                        'speed_dial' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('device_key_type')
                    ->label('Type')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('device_key_label')
                    ->label('Label')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('device_key_protected')
                    ->label('Protected')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('device_key_category')
                    ->label('Category')
                    ->options([
                        'line' => 'Line',
                        'blf' => 'BLF',
                        'speed_dial' => 'Speed Dial',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('device_key_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceKeys::route('/'),
            'create' => Pages\CreateDeviceKey::route('/create'),
            'edit' => Pages\EditDeviceKey::route('/{record}/edit'),
        ];
    }
}
