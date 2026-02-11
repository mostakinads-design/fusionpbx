<?php

namespace App\Filament\Resources\UserLogs;

use App\Filament\Resources\UserLogs\Pages;
use App\Models\UserLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserLogResource extends Resource
{
    protected static ?string $model = UserLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Admin';
    
    protected static ?string $navigationLabel = 'User Logs';
    
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false; // Logs are system generated
    }

    public static function canEdit($record): bool
    {
        return false; // Logs are immutable
    }

    public static function canDelete($record): bool
    {
        return true; // Allow cleanup of old logs
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('timestamp')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('result')
                    ->label('Result')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failure' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('remote_address')
                    ->label('IP Address')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('session_id')
                    ->label('Session')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                    ->options([
                        'success' => 'Success',
                        'failure' => 'Failure',
                    ]),
                
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from'),
                        \Filament\Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('timestamp', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('timestamp', '<=', $date));
                    }),
            ])
            ->defaultSort('timestamp', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserLogs::route('/'),
        ];
    }
}
