<?php

namespace App\Filament\Resources\SipProfiles;

use App\Filament\Resources\SipProfiles\Pages;
use App\Models\SipProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SipProfileResource extends Resource
{
    protected static ?string $model = SipProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'PBX';
    
    protected static ?string $navigationLabel = 'SIP Profiles';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('sip_profile_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Profile Name')
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('sip_profile_hostname')
                            ->maxLength(255)
                            ->label('Hostname'),
                        
                        Forms\Components\Toggle::make('sip_profile_enabled')
                            ->label('Enabled')
                            ->default(true),
                        
                        Forms\Components\Textarea::make('sip_profile_description')
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
                Tables\Columns\TextColumn::make('sip_profile_name')
                    ->label('Profile Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sip_profile_hostname')
                    ->label('Hostname')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('sip_profile_enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('domains_count')
                    ->counts('domains')
                    ->label('Domains')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('settings_count')
                    ->counts('settings')
                    ->label('Settings')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('sip_profile_enabled')
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
            'index' => Pages\ListSipProfiles::route('/'),
            'create' => Pages\CreateSipProfile::route('/create'),
            'edit' => Pages\EditSipProfile::route('/{record}/edit'),
        ];
    }
}
