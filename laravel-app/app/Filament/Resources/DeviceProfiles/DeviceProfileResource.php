<?php

namespace App\Filament\Resources\DeviceProfiles;

use App\Filament\Resources\DeviceProfiles\Pages;
use App\Models\DeviceProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceProfileResource extends Resource
{
    protected static ?string $model = DeviceProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static ?string $navigationGroup = 'Devices';
    
    protected static ?string $navigationLabel = 'Profiles';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('device_profile_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Profile Name')
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Toggle::make('device_profile_enabled')
                            ->label('Enabled')
                            ->default(true),
                        
                        Forms\Components\Textarea::make('device_profile_description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_profile_name')
                    ->label('Profile Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('device_profile_enabled')
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
                Tables\Filters\TernaryFilter::make('device_profile_enabled')
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
            'index' => Pages\ListDeviceProfiles::route('/'),
            'create' => Pages\CreateDeviceProfile::route('/create'),
            'edit' => Pages\EditDeviceProfile::route('/{record}/edit'),
        ];
    }
}
