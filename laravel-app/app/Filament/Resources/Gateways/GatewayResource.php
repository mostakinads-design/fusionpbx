<?php

namespace App\Filament\Resources\Gateways;

use App\Filament\Resources\Gateways\Pages;
use App\Models\Gateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GatewayResource extends Resource
{
    protected static ?string $model = Gateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';
    
    protected static ?string $navigationGroup = 'PBX';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gateway Information')
                    ->schema([
                        Forms\Components\TextInput::make('gateway')
                            ->required()
                            ->maxLength(255)
                            ->label('Gateway Name')
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('username')
                            ->maxLength(255)
                            ->label('Username'),
                        
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->label('Password'),
                        
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Connection Settings')
                    ->schema([
                        Forms\Components\TextInput::make('proxy')
                            ->maxLength(255)
                            ->label('Proxy/Server')
                            ->placeholder('sip.provider.com'),
                        
                        Forms\Components\TextInput::make('register_proxy')
                            ->maxLength(255)
                            ->label('Register Proxy'),
                        
                        Forms\Components\Toggle::make('register')
                            ->label('Register')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('channels')
                            ->numeric()
                            ->label('Max Channels')
                            ->helperText('Maximum concurrent calls'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Settings')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Gateway')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('proxy')
                    ->label('Server')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('register')
                    ->label('Register')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('info')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('channels')
                    ->label('Channels')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled')
                    ->native(false),
                
                Tables\Filters\TernaryFilter::make('register')
                    ->label('Registration')
                    ->boolean()
                    ->trueLabel('Register')
                    ->falseLabel('No Register')
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
            'index' => Pages\ListGateways::route('/'),
            'create' => Pages\CreateGateway::route('/create'),
            'edit' => Pages\EditGateway::route('/{record}/edit'),
        ];
    }
}
