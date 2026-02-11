<?php

namespace App\Filament\Resources\Devices;

use App\Filament\Resources\Devices\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    
    protected static ?string $navigationGroup = 'PBX';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Device Information')
                    ->schema([
                        Forms\Components\TextInput::make('device_mac_address')
                            ->label('MAC Address')
                            ->required()
                            ->maxLength(17)
                            ->unique(ignoreRecord: true)
                            ->placeholder('aa:bb:cc:dd:ee:ff'),
                        
                        Forms\Components\TextInput::make('device_label')
                            ->label('Device Label')
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('device_vendor_uuid')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Vendor'),
                        
                        Forms\Components\TextInput::make('device_model')
                            ->label('Model')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('device_firmware_version')
                            ->label('Firmware Version')
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('device_enabled')
                            ->label('Enabled')
                            ->default(true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Provisioning')
                    ->schema([
                        Forms\Components\Select::make('device_profile_uuid')
                            ->relationship('profile', 'device_profile_name')
                            ->searchable()
                            ->preload()
                            ->label('Device Profile'),
                        
                        Forms\Components\Select::make('device_template')
                            ->label('Template')
                            ->options([
                                'default' => 'Default',
                                'advanced' => 'Advanced',
                                'custom' => 'Custom',
                            ]),
                        
                        Forms\Components\Toggle::make('device_provisioned')
                            ->label('Provisioned')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('device_provisioned_date')
                            ->label('Last Provisioned')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('device_description')
                            ->label('Description')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_mac_address')
                    ->label('MAC Address')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('device_label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('device_model')
                    ->label('Model')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('device_enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('device_provisioned')
                    ->label('Provisioned')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                Tables\Columns\TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Lines')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('device_provisioned_date')
                    ->label('Last Provisioned')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('device_enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled')
                    ->native(false),
                
                Tables\Filters\TernaryFilter::make('device_provisioned')
                    ->label('Provisioned')
                    ->boolean()
                    ->trueLabel('Provisioned')
                    ->falseLabel('Not Provisioned')
                    ->native(false),
                
                Tables\Filters\SelectFilter::make('device_vendor_uuid')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
