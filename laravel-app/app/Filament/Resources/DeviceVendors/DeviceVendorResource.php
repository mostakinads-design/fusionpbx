<?php

namespace App\Filament\Resources\DeviceVendors;

use App\Filament\Resources\DeviceVendors\Pages;
use App\Models\DeviceVendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceVendorResource extends Resource
{
    protected static ?string $model = DeviceVendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationGroup = 'Devices';
    
    protected static ?string $navigationLabel = 'Vendors';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Vendor Name')
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Vendor Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('devices_count')
                    ->counts('devices')
                    ->label('Devices')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled')
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
            'index' => Pages\ListDeviceVendors::route('/'),
            'create' => Pages\CreateDeviceVendor::route('/create'),
            'edit' => Pages\EditDeviceVendor::route('/{record}/edit'),
        ];
    }
}
