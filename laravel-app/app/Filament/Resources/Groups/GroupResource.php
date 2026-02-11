<?php

namespace App\Filament\Resources\Groups;

use App\Filament\Resources\Groups\Pages;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Admin';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('group_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Group Name'),
                        
                        Forms\Components\TextInput::make('group_level')
                            ->numeric()
                            ->default(0)
                            ->label('Group Level')
                            ->helperText('Higher levels have more privileges'),
                        
                        Forms\Components\Toggle::make('group_protected')
                            ->label('Protected')
                            ->helperText('Protected groups cannot be deleted')
                            ->default(false),
                        
                        Forms\Components\Textarea::make('group_description')
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
                Tables\Columns\TextColumn::make('group_name')
                    ->label('Group Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('group_level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'danger',
                        $state >= 50 => 'warning',
                        default => 'info',
                    })
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('group_protected')
                    ->label('Protected')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('group_protected')
                    ->label('Protected')
                    ->boolean()
                    ->trueLabel('Protected Only')
                    ->falseLabel('Not Protected')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->disabled(fn (Group $record): bool => $record->group_protected === 'true'),
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
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
