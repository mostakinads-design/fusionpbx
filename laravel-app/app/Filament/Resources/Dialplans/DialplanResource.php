<?php

namespace App\Filament\Resources\Dialplans;

use App\Filament\Resources\Dialplans\Pages;
use App\Models\Dialplan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DialplanResource extends Resource
{
    protected static ?string $model = Dialplan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationGroup = 'PBX';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dialplan Information')
                    ->schema([
                        Forms\Components\TextInput::make('dialplan_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Name'),
                        
                        Forms\Components\TextInput::make('dialplan_number')
                            ->maxLength(255)
                            ->label('Number/Pattern')
                            ->helperText('Extension number or regex pattern'),
                        
                        Forms\Components\TextInput::make('dialplan_context')
                            ->required()
                            ->maxLength(255)
                            ->label('Context')
                            ->default('default'),
                        
                        Forms\Components\Toggle::make('dialplan_enabled')
                            ->label('Enabled')
                            ->default(true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Execution')
                    ->schema([
                        Forms\Components\TextInput::make('dialplan_order')
                            ->numeric()
                            ->default(100)
                            ->label('Order')
                            ->helperText('Lower numbers execute first'),
                        
                        Forms\Components\Toggle::make('dialplan_continue')
                            ->label('Continue')
                            ->helperText('Continue to next dialplan after execution')
                            ->default(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('dialplan_description')
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
                Tables\Columns\TextColumn::make('dialplan_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dialplan_number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dialplan_context')
                    ->label('Context')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dialplan_order')
                    ->label('Order')
                    ->sortable()
                    ->badge(),
                
                Tables\Columns\IconColumn::make('dialplan_enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('dialplan_continue')
                    ->label('Continue')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-right-circle')
                    ->falseIcon('heroicon-o-stop-circle')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('details_count')
                    ->counts('details')
                    ->label('Rules')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('dialplan_enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled')
                    ->native(false),
                
                Tables\Filters\SelectFilter::make('dialplan_context')
                    ->label('Context')
                    ->options(fn () => Dialplan::query()
                        ->distinct()
                        ->pluck('dialplan_context', 'dialplan_context')
                        ->toArray()
                    ),
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
            ->defaultSort('dialplan_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDialplans::route('/'),
            'create' => Pages\CreateDialplan::route('/create'),
            'edit' => Pages\EditDialplan::route('/{record}/edit'),
        ];
    }
}
