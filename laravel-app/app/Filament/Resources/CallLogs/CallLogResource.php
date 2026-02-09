<?php

namespace App\Filament\Resources\CallLogs;

use App\Filament\Resources\CallLogs\Pages\ManageCallLogs;
use App\Models\CallLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CallLogResource extends Resource
{
    protected static ?string $model = CallLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Call Logs';

    protected static ?string $modelLabel = 'Call Log';

    protected static ?string $pluralModelLabel = 'Call Logs';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Call Information')
                    ->description('View call log details')
                    ->schema([
                        TextInput::make('caller_id')
                            ->label('Caller ID')
                            ->disabled(),

                        TextInput::make('destination')
                            ->label('Destination')
                            ->disabled(),

                        TextInput::make('duration')
                            ->label('Duration (seconds)')
                            ->disabled(),

                        Select::make('status')
                            ->options([
                                'completed' => 'Completed',
                                'missed' => 'Missed',
                                'busy' => 'Busy',
                                'failed' => 'Failed',
                            ])
                            ->disabled(),

                        Select::make('call_type')
                            ->options([
                                'inbound' => 'Inbound',
                                'outbound' => 'Outbound',
                                'internal' => 'Internal',
                            ])
                            ->disabled(),

                        DateTimePicker::make('call_date')
                            ->label('Call Date')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('caller_id')
                    ->searchable()
                    ->sortable()
                    ->label('Caller ID')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Caller ID copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('destination')
                    ->searchable()
                    ->sortable()
                    ->label('Destination')
                    ->copyable()
                    ->copyMessage('Destination copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('duration')
                    ->sortable()
                    ->label('Duration')
                    ->formatStateUsing(fn ($state): string => gmdate('H:i:s', $state))
                    ->description(fn ($state): string => $state . ' seconds')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->sortable()
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'missed' => 'warning',
                        'busy' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('call_type')
                    ->sortable()
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inbound' => 'primary',
                        'outbound' => 'warning',
                        'internal' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('call_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Call Date')
                    ->description(fn ($state): string => $state->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'missed' => 'Missed',
                        'busy' => 'Busy',
                        'failed' => 'Failed',
                    ])
                    ->label('Filter by Status'),

                SelectFilter::make('call_type')
                    ->options([
                        'inbound' => 'Inbound',
                        'outbound' => 'Outbound',
                        'internal' => 'Internal',
                    ])
                    ->label('Filter by Type'),

                Filter::make('call_date')
                    ->form([
                        DateTimePicker::make('from')
                            ->label('From Date'),
                        DateTimePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('call_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('call_date', '<=', $date),
                            );
                    })
                    ->label('Date Range'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // No bulk actions for read-only resource
                ]),
            ])
            ->defaultSort('call_date', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCallLogs::route('/'),
        ];
    }
}
