<?php

namespace App\Filament\Resources\DialplanDetails;

use App\Filament\Resources\DialplanDetails\Pages;
use App\Models\DialplanDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DialplanDetailResource extends Resource
{
    protected static ?string $model = DialplanDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    
    protected static ?string $navigationGroup = 'PBX';
    
    protected static ?string $navigationLabel = 'Dialplan Rules';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('dialplan_uuid')
                            ->relationship('dialplan', 'dialplan_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Dialplan'),
                        
                        Forms\Components\Select::make('dialplan_detail_tag')
                            ->options([
                                'condition' => 'Condition',
                                'action' => 'Action',
                                'anti-action' => 'Anti-Action',
                            ])
                            ->required()
                            ->label('Type'),
                        
                        Forms\Components\TextInput::make('dialplan_detail_type')
                            ->maxLength(255)
                            ->label('Field')
                            ->helperText('e.g., destination_number, caller_id_number'),
                        
                        Forms\Components\TextInput::make('dialplan_detail_data')
                            ->maxLength(255)
                            ->label('Expression/Value'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Execution')
                    ->schema([
                        Forms\Components\TextInput::make('dialplan_detail_group')
                            ->numeric()
                            ->default(0)
                            ->label('Group'),
                        
                        Forms\Components\TextInput::make('dialplan_detail_order')
                            ->numeric()
                            ->default(100)
                            ->label('Order'),
                        
                        Forms\Components\Toggle::make('dialplan_detail_break')
                            ->label('Break on Match')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('dialplan_detail_inline')
                            ->label('Inline')
                            ->default(false),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dialplan.dialplan_name')
                    ->label('Dialplan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dialplan_detail_tag')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'condition' => 'info',
                        'action' => 'success',
                        'anti-action' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('dialplan_detail_type')
                    ->label('Field')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('dialplan_detail_data')
                    ->label('Value')
                    ->limit(40)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('dialplan_detail_group')
                    ->label('Group')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dialplan_detail_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dialplan_detail_tag')
                    ->label('Type')
                    ->options([
                        'condition' => 'Condition',
                        'action' => 'Action',
                        'anti-action' => 'Anti-Action',
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
            ->defaultSort('dialplan_detail_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDialplanDetails::route('/'),
            'create' => Pages\CreateDialplanDetail::route('/create'),
            'edit' => Pages\EditDialplanDetail::route('/{record}/edit'),
        ];
    }
}
