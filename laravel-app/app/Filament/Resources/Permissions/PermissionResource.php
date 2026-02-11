<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages;
use App\Models\Permission;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    
    protected static ?string $navigationGroup = 'Admin';
    
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false; // Permissions are managed by code
    }

    public static function canEdit($record): bool
    {
        return false; // Permissions are managed by code
    }

    public static function canDelete($record): bool
    {
        return false; // Permissions are managed by code
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permission_name')
                    ->label('Permission')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('application_name')
                    ->label('Application')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('permission_description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('insert_date')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('application_name')
                    ->label('Application')
                    ->options(fn () => Permission::query()
                        ->distinct()
                        ->pluck('application_name', 'application_name')
                        ->toArray()
                    ),
            ])
            ->defaultSort('permission_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }
}
