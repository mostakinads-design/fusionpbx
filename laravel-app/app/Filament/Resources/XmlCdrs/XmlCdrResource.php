<?php

namespace App\Filament\Resources\XmlCdrs;

use App\Filament\Resources\XmlCdrs\Pages;
use App\Models\XmlCdr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class XmlCdrResource extends Resource
{
    protected static ?string $model = XmlCdr::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-down-left';
    
    protected static ?string $navigationGroup = 'Monitoring';
    
    protected static ?string $navigationLabel = 'Call Records';
    
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false; // CDRs are system generated
    }

    public static function canEdit($record): bool
    {
        return false; // CDRs are immutable
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_stamp')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inbound' => 'info',
                        'outbound' => 'success',
                        'local' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('caller_id_number')
                    ->label('Caller')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('caller_destination')
                    ->label('Destination')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('billsec')
                    ->label('Bill Sec')
                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('hangup_cause')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'NORMAL_CLEARING' => 'success',
                        'USER_BUSY' => 'warning',
                        'NO_ANSWER' => 'danger',
                        'CALL_REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', $state)),
                
                Tables\Columns\IconColumn::make('record_path')
                    ->label('Recording')
                    ->boolean()
                    ->trueIcon('heroicon-o-microphone')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => !empty($record->record_path)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('direction')
                    ->options([
                        'inbound' => 'Inbound',
                        'outbound' => 'Outbound',
                        'local' => 'Local',
                    ]),
                
                Tables\Filters\SelectFilter::make('hangup_cause')
                    ->label('Status')
                    ->options([
                        'NORMAL_CLEARING' => 'Answered',
                        'NO_ANSWER' => 'No Answer',
                        'USER_BUSY' => 'Busy',
                        'CALL_REJECTED' => 'Rejected',
                    ]),
                
                Tables\Filters\Filter::make('start_stamp')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('start_stamp', '>=', $date))
                            ->when($data['date_to'], fn ($q, $date) => $q->whereDate('start_stamp', '<=', $date));
                    }),
                
                Tables\Filters\TernaryFilter::make('has_recording')
                    ->label('Has Recording')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('record_path')->where('record_path', '!=', ''),
                        false: fn ($query) => $query->where(fn ($q) => $q->whereNull('record_path')->orWhere('record_path', '=')),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_stamp', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListXmlCdrs::route('/'),
            'view' => Pages\ViewXmlCdr::route('/{record}'),
        ];
    }
}
